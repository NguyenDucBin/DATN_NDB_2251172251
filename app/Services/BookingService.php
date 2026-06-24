<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function create(User $user, int $tourId, array $data): Booking
    {
        return DB::transaction(function () use ($user, $tourId, $data) {
            $tour = Tour::whereKey($tourId)->lockForUpdate()->firstOrFail();

            if ($tour->status !== 'approved' || ! $tour->is_active) {
                throw ValidationException::withMessages([
                    'tour' => 'Tour này hiện không còn mở bán.',
                ]);
            }

            $bookedPeople = Booking::where('tour_id', $tour->id)
                ->whereDate('start_date', $data['start_date'])
                ->whereIn('status', ['pending', 'confirmed', 'completed'])
                ->sum('number_of_people');

            $remainingCapacity = max(0, $tour->capacity - $bookedPeople);

            if ((int) $data['number_of_people'] > $remainingCapacity) {
                throw ValidationException::withMessages([
                    'number_of_people' => $remainingCapacity > 0
                        ? "Ngày này chỉ còn {$remainingCapacity} chỗ trống."
                        : 'Ngày khởi hành này đã hết chỗ.',
                ]);
            }

            $subtotal = (float) $tour->price * (int) $data['number_of_people'];
            [$totalPrice, $coupon] = $this->applyCoupon($subtotal, $data['coupon_code'] ?? null);

            $booking = Booking::create([
                'user_id' => $user->id,
                'tour_id' => $tour->id,
                'start_date' => $data['start_date'],
                'number_of_people' => $data['number_of_people'],
                'total_price' => $totalPrice,
                'payment_method' => $data['payment_method'],
                'status' => 'pending',
                'payment_status' => $data['payment_method'] === 'bank_transfer' && config('payment.test_mode')
                    ? 'paid'
                    : 'pending',
            ]);

            if ($coupon) {
                $coupon->increment('used_count');
            }

            if ($user->phone !== $data['phone']) {
                $user->update(['phone' => $data['phone']]);
            }

            return $booking;
        }, 3);
    }

    private function applyCoupon(float $subtotal, ?string $code): array
    {
        if (! $code) {
            return [$subtotal, null];
        }

        $coupon = Coupon::where('code', strtoupper(trim($code)))->lockForUpdate()->first();

        if (! $coupon || ! $coupon->is_active) {
            throw ValidationException::withMessages(['coupon_code' => 'Mã giảm giá không tồn tại hoặc đã ngừng sử dụng.']);
        }

        if ($coupon->valid_from && now()->lt($coupon->valid_from)) {
            throw ValidationException::withMessages(['coupon_code' => 'Mã giảm giá chưa đến thời gian sử dụng.']);
        }

        if ($coupon->valid_until && now()->gt($coupon->valid_until)) {
            throw ValidationException::withMessages(['coupon_code' => 'Mã giảm giá đã hết hạn.']);
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            throw ValidationException::withMessages(['coupon_code' => 'Mã giảm giá đã hết lượt sử dụng.']);
        }

        $discount = $coupon->discount_type === 'percent'
            ? $subtotal * min(100, (float) $coupon->discount_amount) / 100
            : min($subtotal, (float) $coupon->discount_amount);

        return [max(0, round($subtotal - $discount, 2)), $coupon];
    }
}
