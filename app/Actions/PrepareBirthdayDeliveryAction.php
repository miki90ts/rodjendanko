<?php

namespace App\Actions;

use App\Enums\DeliveryStatus;
use App\Enums\DeliveryType;
use App\Enums\MessageMode;
use App\Enums\NotificationChannel;
use App\Jobs\SendBirthdayDeliveryJob;
use App\Models\BirthdayContact;
use App\Models\BirthdayDelivery;
use App\Models\BirthdayReminder;
use App\Services\BirthdayMessageRenderer;
use App\Services\BirthdayMessageSelector;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PrepareBirthdayDeliveryAction
{
    public function __construct(private readonly BirthdayMessageSelector $selector, private readonly BirthdayMessageRenderer $renderer) {}

    public function execute(BirthdayContact $contact, DeliveryType $type, int $birthdayYear, Carbon $scheduledFor, ?BirthdayReminder $reminder = null): BirthdayDelivery
    {
        $occurrenceKey = $type === DeliveryType::Greeting ? 'greeting' : "reminder:{$reminder->days_before}";
        $settings = $contact->settings;
        $message = $type === DeliveryType::Greeting ? $this->selector->select($contact) : null;
        $raw = $type === DeliveryType::Reminder
            ? "Za {$reminder->days_before} dana je rođendan za {$contact->display_name}."
            : ($settings->message_mode === MessageMode::Custom ? $settings->custom_message : $message?->body);
        $template = $settings?->emailTemplate ?? $contact->user->birthdaySettings?->defaultEmailTemplate;
        $subject = $type === DeliveryType::Reminder
            ? "Podsetnik: {$contact->display_name} slavi rođendan"
            : $this->renderer->render($settings?->email_subject ?: 'Srećan rođendan, {{first_name}}! 🎉', $contact);

        $delivery = $contact->deliveries()->firstOrCreate(
            ['type' => $type->value, 'birthday_year' => $birthdayYear, 'occurrence_key' => $occurrenceKey],
            [
                'idempotency_key' => (string) Str::uuid(),
                'user_id' => $contact->user_id,
                'channel' => NotificationChannel::Email,
                'message_id' => $message?->id,
                'email_template_id' => $template?->id,
                'scheduled_for' => $scheduledFor->utc(),
                'status' => DeliveryStatus::Pending,
                'recipient' => $type === DeliveryType::Reminder ? $contact->user->email : (string) $contact->email,
                'subject' => $subject,
                'rendered_message' => $this->renderer->render((string) $raw, $contact),
            ],
        );

        if ($delivery->wasRecentlyCreated) {
            SendBirthdayDeliveryJob::dispatch($delivery->id)->afterCommit();
        }

        return $delivery;
    }
}
