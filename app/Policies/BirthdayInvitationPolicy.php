<?php

namespace App\Policies;

use App\Models\BirthdayInvitation;
use App\Models\User;

class BirthdayInvitationPolicy
{
    public function view(User $user, BirthdayInvitation $invitation): bool
    {
        return $invitation->user_id === $user->id;
    }

    public function update(User $user, BirthdayInvitation $invitation): bool
    {
        return $invitation->user_id === $user->id;
    }

    public function delete(User $user, BirthdayInvitation $invitation): bool
    {
        return $invitation->user_id === $user->id;
    }
}
