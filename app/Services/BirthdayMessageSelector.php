<?php

namespace App\Services;

use App\Enums\DeliveryStatus;
use App\Enums\DeliveryType;
use App\Enums\MessageMode;
use App\Models\BirthdayContact;
use App\Models\BirthdayMessage;
use Illuminate\Validation\ValidationException;

class BirthdayMessageSelector
{
    public function select(BirthdayContact $contact): ?BirthdayMessage
    {
        $settings = $contact->settings;

        if ($settings?->message_mode === MessageMode::Selected) {
            return $settings->selectedMessage
                ?? throw ValidationException::withMessages(['message' => 'Izabrana poruka više nije dostupna.']);
        }

        if ($settings?->message_mode === MessageMode::Custom) {
            return null;
        }

        $query = BirthdayMessage::query()->availableTo($contact->user_id)->where('is_active', true);

        if ($settings?->message_category) {
            $query->where('category', $settings->message_category->value);
        }
        if ($settings?->message_tone) {
            $query->where('tone', $settings->message_tone->value);
        }

        $recentIds = $contact->deliveries()
            ->where('type', DeliveryType::Greeting->value)
            ->where('status', DeliveryStatus::Sent->value)
            ->whereNotNull('message_id')
            ->latest('sent_at')
            ->limit(3)
            ->pluck('message_id');

        $message = (clone $query)->whereNotIn('id', $recentIds)->inRandomOrder()->first()
            ?? $query->inRandomOrder()->first();

        if (! $message) {
            throw ValidationException::withMessages(['message' => 'Nema dostupnih poruka za izabrane filtere.']);
        }

        return $message;
    }
}
