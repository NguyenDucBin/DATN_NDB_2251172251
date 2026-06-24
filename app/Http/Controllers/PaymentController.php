<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function createPayment(Request $request, $bookingId)
    {
        $booking = Booking::with('tour')->findOrFail($bookingId);

        abort_unless($booking->user_id === $request->user()?->id, 403);

        if ($booking->status !== 'pending' || in_array($booking->payment_status, ['paid', 'refunded'], true)) {
            return back()->with('error', 'Booking này không thể thực hiện thanh toán lại.');
        }

        if (! config('vnpay.vnp_TmnCode') || ! config('vnpay.vnp_HashSecret')) {
            return back()->with('error', 'Cổng VNPay chưa được cấu hình. Vui lòng chọn phương thức khác.');
        }

        $booking->update([
            'payment_method' => 'vnpay',
            'payment_status' => 'pending',
        ]);

        $vnp_TmnCode = config('vnpay.vnp_TmnCode');
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $vnp_Url = config('vnpay.vnp_Url');
        $vnp_Returnurl = config('vnpay.vnp_Returnurl');

        $vnp_TxnRef = $booking->id.'_'.time(); // Mã đơn hàng
        $vnp_OrderInfo = 'Thanh toan don hang tour: '.$booking->tour->name;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $booking->total_price * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = ''; // Để trống để người dùng chọn thẻ
        $vnp_IpAddr = $request->ip();

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnp_TmnCode,
            'vnp_Amount' => $vnp_Amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => $vnp_Locale,
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => $vnp_OrderType,
            'vnp_ReturnUrl' => $vnp_Returnurl,
            'vnp_TxnRef' => $vnp_TxnRef,
        ];

        if (isset($vnp_BankCode) && $vnp_BankCode != '') {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = '';
        $i = 0;
        $hashdata = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&'.urlencode($key).'='.urlencode($value);
            } else {
                $hashdata .= urlencode($key).'='.urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key).'='.urlencode($value).'&';
        }

        $vnp_Url = $vnp_Url.'?'.$query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash='.$vnpSecureHash;

        return redirect($vnp_Url);
    }

    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');

        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == 'vnp_') {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        if ($vnp_SecureHash === '' || empty($inputData['vnp_TxnRef']) || ! $vnp_HashSecret) {
            return redirect()->route('home')->with('error', 'Thông tin thanh toán không hợp lệ.');
        }

        ksort($inputData);
        $i = 0;
        $hashData = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData.'&'.urlencode($key).'='.urlencode($value);
            } else {
                $hashData = $hashData.urlencode($key).'='.urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if (! hash_equals(strtolower($secureHash), strtolower($vnp_SecureHash))) {
            return redirect()->route('home')->with('error', 'Chữ ký không hợp lệ.');
        }

        $bookingId = explode('_', $inputData['vnp_TxnRef'])[0] ?? '';

        if (! ctype_digit((string) $bookingId)) {
            return redirect()->route('home')->with('error', 'Mã booking không hợp lệ.');
        }

        $booking = Booking::find($bookingId);

        if (! $booking) {
            return redirect()->route('home')->with('error', 'Không tìm thấy booking cần thanh toán.');
        }

        $expectedAmount = (int) round((float) $booking->total_price * 100);
        $receivedAmount = (int) ($inputData['vnp_Amount'] ?? 0);
        $receivedTmnCode = $inputData['vnp_TmnCode'] ?? '';

        if ($receivedAmount !== $expectedAmount || $receivedTmnCode !== config('vnpay.vnp_TmnCode')) {
            return redirect()->route('home')->with('error', 'Số tiền hoặc đơn vị thanh toán không khớp với booking.');
        }

        if (($inputData['vnp_ResponseCode'] ?? '') === '00') {
            $result = DB::transaction(function () use ($booking) {
                $locked = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();

                if ($locked->payment_status === 'paid') {
                    return $locked;
                }

                if ($locked->status !== 'pending' || $locked->payment_status === 'refunded') {
                    return null;
                }

                $locked->update(['payment_status' => 'paid', 'payment_method' => 'vnpay']);

                return $locked;
            }, 3);

            if (! $result) {
                return redirect()->route('home')->with('error', 'Booking không còn ở trạng thái có thể thanh toán.');
            }

            return redirect()->route('booking.success', $booking->id)
                ->with('success', 'Thanh toán thành công. Booking đang chờ Host xác nhận.');
        }

        DB::transaction(function () use ($booking) {
            $locked = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();

            if ($locked->status === 'pending' && $locked->payment_status !== 'paid') {
                $locked->update(['status' => 'cancelled', 'payment_status' => 'failed']);
            }
        }, 3);

        return redirect()->route('home')->with('error', 'Thanh toán thất bại hoặc đã bị hủy.');
    }
}
