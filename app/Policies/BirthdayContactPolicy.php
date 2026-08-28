<?php

namespace App\Policies;

use App\Models\BirthdayContact;
use App\Models\User;

class BirthdayContactPolicy
{
    public function view(User $user, BirthdayContact $contact): bool { return $user->id === $contact->user_id; }
    public function update(User $user, BirthdayContact $contact): bool { return $user->id === $contact->user_id; }
    public function delete(User $user, BirthdayContact $contact): bool { return $user->id === $contact->user_id; }
}
