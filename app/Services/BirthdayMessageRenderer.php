<?php

namespace App\Services;

use App\Models\BirthdayContact;

class BirthdayMessageRenderer
{
    public function render(string $message, BirthdayContact $contact): string
    {
        return strtr($message, [
            '{{first_name}}' => $contact->first_name,
            '{{last_name}}' => $contact->last_name ?? '',
            '{{nickname}}' => $contact->nickname ?? $contact->first_name,
        ]);
    }
}
