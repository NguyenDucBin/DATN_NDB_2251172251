<?php

namespace App\Policies;

use App\Models\Tour;
use App\Models\User;

class TourPolicy
{
    public function update(User $user, Tour $tour): bool
    {
        return $user->hasRole('host') && (int) $tour->host_id === (int) $user->id;
    }

    public function delete(User $user, Tour $tour): bool
    {
        return $this->update($user, $tour);
    }
}
