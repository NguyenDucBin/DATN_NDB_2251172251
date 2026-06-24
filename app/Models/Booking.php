<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'tour_id', 'start_date', 'number_of_people', 'total_price',
        'status', 'payment_status', 'payment_method',
    ];

    protected $casts = [
        'start_date' => 'date',
        'total_price' => 'decimal:2',
        'number_of_people' => 'integer',
    ];

    public function statusLabel(): string
    {
        if ($this->hasPendingRefundRequest()) {
            return 'Yêu cầu hoàn tiền';
        }

        return match ($this->status) {
            'pending' => match ($this->payment_status) {
                'paid' => 'Chờ Host xác nhận',
                'pending' => $this->payment_method === 'bank_transfer'
                    ? 'Chờ xác nhận chuyển khoản'
                    : 'Chờ thanh toán',
                'failed' => 'Thanh toán lỗi',
                default => 'Chờ xử lý',
            },
            'confirmed' => 'Đã xác nhận',
            'completed' => 'Hoàn tất',
            'cancelled' => 'Đã hủy',
            'refund_requested' => 'Yêu cầu hoàn tiền',
            'refunded' => 'Đã hoàn tiền',
            default => 'Không xác định',
        };
    }

    public function statusBadgeClasses(): string
    {
        if ($this->hasPendingRefundRequest()) {
            return 'text-purple-700 bg-purple-50 border-purple-200';
        }

        return match ($this->status) {
            'pending' => match ($this->payment_status) {
                'paid' => 'text-blue-700 bg-blue-50 border-blue-200',
                'failed' => 'text-red-700 bg-red-50 border-red-200',
                default => 'text-amber-700 bg-amber-50 border-amber-200',
            },
            'confirmed' => 'text-indigo-700 bg-indigo-50 border-indigo-200',
            'completed' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
            'cancelled' => 'text-red-700 bg-red-50 border-red-200',
            'refund_requested' => 'text-purple-700 bg-purple-50 border-purple-200',
            'refunded' => 'text-gray-700 bg-gray-100 border-gray-200',
            default => 'text-gray-700 bg-gray-50 border-gray-200',
        };
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Đã thanh toán',
            'pending' => 'Đang chờ thanh toán',
            'failed' => 'Thanh toán thất bại',
            'refunded' => 'Đã hoàn tiền',
            default => 'Chưa thanh toán',
        };
    }

    public function paymentStatusBadgeClasses(): string
    {
        return match ($this->payment_status) {
            'paid' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
            'pending' => 'text-amber-700 bg-amber-50 border-amber-200',
            'failed' => 'text-red-700 bg-red-50 border-red-200',
            'refunded' => 'text-gray-700 bg-gray-100 border-gray-200',
            default => 'text-slate-700 bg-slate-50 border-slate-200',
        };
    }

    public function canBeConfirmedByHost(): bool
    {
        return $this->status === 'pending'
            && $this->payment_status === 'paid'
            && ! $this->hasPendingRefundRequest();
    }

    public function canBeCompletedByHost(): bool
    {
        return $this->status === 'confirmed' && ! $this->hasPendingRefundRequest();
    }

    public function canBeCancelledByHost(): bool
    {
        return in_array($this->status, ['pending', 'confirmed'], true)
            && ! $this->hasPendingRefundRequest();
    }

    public function canRequestRefund(): bool
    {
        return $this->payment_status === 'paid'
            && in_array($this->status, ['pending', 'confirmed', 'completed'], true)
            && ! $this->hasPendingRefundRequest();
    }

    public function hasPendingRefundRequest(): bool
    {
        if ($this->relationLoaded('refunds')) {
            return $this->refunds->contains('status', 'pending');
        }

        return $this->refunds()->where('status', 'pending')->exists();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }
}
