<?php

namespace App\Actions;

use App\Models\BirthdayContact;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UpdateBirthdayContactAction
{
    public function execute(BirthdayContact $contact, array $data): BirthdayContact
    {
        return DB::transaction(function () use ($contact, $data): BirthdayContact {
            $contact->update(Arr::except($data, ['settings', 'reminders']));
            $contact->settings()->updateOrCreate([], $data['settings']);
            $contact->reminders()->delete();
            $contact->reminders()->createMany(collect($data['reminders'] ?? [])->map(fn (int|array $reminder) => is_array($reminder) ? $reminder : ['days_before' => $reminder])->all());

            return $contact->refresh()->load(['settings', 'reminders']);
        });
    }
}
