<?php

namespace App\Policies;

use App\Models\BirthdayMessage;
use App\Models\User;

class BirthdayMessagePolicy
{
    public function update(User $user, BirthdayMessage $message): bool { return $message->user_id !== null && $message->user_id === $user->id; }
    public function delete(User $user, BirthdayMessage $message): bool { return $message->user_id !== null && $message->user_id === $user->id; }
}
