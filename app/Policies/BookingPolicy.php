<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->hasRole('admin')
            || (int) $booking->user_id === (int) $user->id
            || ($user->hasRole('host') && (int) $booking->tour?->host_id === (int) $user->id);
    }

    public function manage(User $user, Booking $booking): bool
    {
        return $user->hasRole('host') && (int) $booking->tour?->host_id === (int) $user->id;
    }

    public function requestRefund(User $user, Booking $booking): bool
    {
        return (int) $booking->user_id === (int) $user->id;
    }
}
