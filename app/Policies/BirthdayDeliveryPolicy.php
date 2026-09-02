<?php

namespace App\Policies;

use App\Models\BirthdayDelivery;
use App\Models\User;

class BirthdayDeliveryPolicy
{
    public function view(User $user, BirthdayDelivery $delivery): bool
    {
        return $user->id === $delivery->user_id;
    }

    public function update(User $user, BirthdayDelivery $delivery): bool
    {
        return $user->id === $delivery->user_id;
    }
}
