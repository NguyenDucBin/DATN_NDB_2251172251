<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Refund;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BookingWorkflowService
{
    public function markPaid(Booking $booking): bool
    {
        return DB::transaction(function () use ($booking) {
            $locked = $this->lockedBooking($booking);

            if (in_array($locked->status, ['cancelled', 'completed', 'refunded'], true)) {
                throw new RuntimeException('Không thể xác nhận thanh toán cho booking đã hủy, hoàn tất hoặc hoàn tiền.');
            }

            if ($locked->payment_status === 'paid') {
                return false;
            }

            $locked->update(['payment_status' => 'paid']);

            return true;
        }, 3);
    }

    public function confirm(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {
            $locked = $this->lockedBooking($booking);

            if (! $locked->canBeConfirmedByHost()) {
                throw new RuntimeException('Booking này chưa đủ điều kiện xác nhận. Hãy kiểm tra trạng thái thanh toán trước.');
            }

            $locked->update(['status' => 'confirmed']);

            return $locked;
        }, 3);
    }

    public function complete(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {
            $locked = $this->lockedBooking($booking);

            if (! $locked->canBeCompletedByHost()) {
                throw new RuntimeException('Chỉ có thể hoàn tất booking đã được xác nhận.');
            }

            $locked->update(['status' => 'completed']);

            return $locked;
        }, 3);
    }

    public function cancel(Booking $booking, string $reason): bool
    {
        return DB::transaction(function () use ($booking, $reason) {
            $locked = $this->lockedBooking($booking);

            if (! $locked->canBeCancelledByHost()) {
                throw new RuntimeException('Booking này không thể hủy ở trạng thái hiện tại.');
            }

            $locked->update([
                'status' => 'cancelled',
                'payment_status' => $locked->payment_status === 'paid' ? 'paid' : 'failed',
            ]);

            if ($locked->payment_status !== 'paid') {
                return false;
            }

            Refund::create([
                'booking_id' => $locked->id,
                'amount' => $locked->total_price,
                'reason' => $reason,
                'status' => 'pending',
            ]);

            return true;
        }, 3);
    }

    public function requestRefund(Booking $booking, string $reason): Refund
    {
        return DB::transaction(function () use ($booking, $reason) {
            $locked = $this->lockedBooking($booking);

            if (! $locked->canRequestRefund()) {
                throw new RuntimeException('Booking này chưa đủ điều kiện hoặc đã có yêu cầu hoàn tiền đang xử lý.');
            }

            return Refund::create([
                'booking_id' => $locked->id,
                'amount' => $locked->total_price,
                'reason' => $reason,
                'status' => 'pending',
            ]);
        }, 3);
    }

    public function processRefund(Refund $refund, string $status): Refund
    {
        return DB::transaction(function () use ($refund, $status) {
            $lockedRefund = Refund::whereKey($refund->id)->lockForUpdate()->firstOrFail();

            if ($lockedRefund->status !== 'pending') {
                throw new RuntimeException('Yêu cầu hoàn tiền này đã được xử lý trước đó.');
            }

            $booking = Booking::whereKey($lockedRefund->booking_id)->lockForUpdate()->firstOrFail();
            $lockedRefund->update(['status' => $status]);

            if ($status === 'processed') {
                $booking->update(['status' => 'refunded', 'payment_status' => 'refunded']);
            }

            return $lockedRefund;
        }, 3);
    }

    private function lockedBooking(Booking $booking): Booking
    {
        return Booking::whereKey($booking->id)
            ->with('refunds')
            ->lockForUpdate()
            ->firstOrFail();
    }
}
