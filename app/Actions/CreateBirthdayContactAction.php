<?php

namespace App\Actions;

use App\Models\BirthdayContact;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CreateBirthdayContactAction
{
    public function execute(User $user, array $data): BirthdayContact
    {
        return DB::transaction(function () use ($user, $data): BirthdayContact {
            $contact = $user->birthdayContacts()->create(Arr::except($data, ['settings', 'reminders']));
            $contact->settings()->create($data['settings']);
            $contact->reminders()->createMany(collect($data['reminders'] ?? [])->map(fn (int|array $reminder) => is_array($reminder) ? $reminder : ['days_before' => $reminder])->all());

            return $contact->load(['settings', 'reminders']);
        });
    }
}
