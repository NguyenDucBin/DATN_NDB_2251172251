<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Tour;
use App\Services\BookingService;

class BookingController extends Controller
{
    public function __construct(private readonly BookingService $bookingService) {}

    // Hiển thị trang Checkout (Form điền thông tin & Chọn thanh toán)
    public function checkout($slug)
    {
        $tour = Tour::where('slug', $slug)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->firstOrFail();

        return view('booking.checkout', compact('tour'));
    }

    // Xử lý lưu dữ liệu khi bấm "Xác nhận thanh toán"
    public function store(StoreBookingRequest $request, $tour_id)
    {
        $booking = $this->bookingService->create(
            $request->user(),
            (int) $tour_id,
            $request->validated(),
        );

        // Nếu chọn VNPay thì gọi hàm xử lý VNPay tại đây
        if ($booking->payment_method === 'vnpay') {
            return redirect()->route('vnpay.payment', $booking->id);
        }

        // Tạm thời redirect sang trang Thành công
        return redirect()->route('booking.success', $booking->id);
    }

    // Trang thông báo đặt tour thành công
    public function success($id)
    {
        $booking = Booking::with('tour')->findOrFail($id);
        $this->authorize('view', $booking);

        return view('booking.success', compact('booking'));
    }
}
