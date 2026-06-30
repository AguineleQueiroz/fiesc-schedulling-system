<?php

namespace App\Policies;

use App\Models\Availability;
use App\Models\User;

class AvailabilityPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Availability $availability): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Availability $availability): bool
    {
        return $user->isAdmin();
    }
}
