<?php

namespace App\Mail;

use App\Enums\DeliveryType;
use App\Enums\MessageMode;
use App\Models\BirthdayDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class BirthdayDeliveryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly BirthdayDelivery $delivery) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->delivery->subject ?? 'Rođendanski podsetnik');
    }

    public function content(): Content
    {
        $contact = $this->delivery->contact;
        $settings = $contact?->settings;
        $daysBefore = Str::startsWith((string) $this->delivery->occurrence_key, 'reminder:')
            ? (int) Str::after((string) $this->delivery->occurrence_key, 'reminder:')
            : null;
        $messagePlan = match (true) {
            ! $settings?->auto_send_enabled => [
                'label' => 'Ručna poruka',
                'description' => 'Ti unosiš i šalješ čestitku kada budeš spreman/na.',
            ],
            $settings->message_mode === MessageMode::Custom => [
                'label' => 'Tvoja unapred uneta poruka',
                'description' => 'Tvoja lična čestitka biće poslata automatski.',
            ],
            $settings->message_mode === MessageMode::Selected => [
                'label' => 'Izabrana automatska poruka',
                'description' => 'Izabrana čestitka biće poslata automatski.',
            ],
            default => [
                'label' => 'Automatska poruka',
                'description' => 'Rođendanko će odabrati i poslati čestitku automatski.',
            ],
        };
        $view = $this->delivery->type === DeliveryType::Reminder
            ? 'mail.reminder'
            : ($this->delivery->emailTemplate?->view ?? 'mail.birthdays.minimal');

        return new Content(view: $view, with: [
            'contact' => $contact,
            'messageText' => $this->delivery->rendered_message,
            'template' => $this->delivery->emailTemplate,
            'senderName' => $contact?->user?->name ?? 'Rođendanko',
            'birthdayDate' => $contact?->birth_date?->copy()->setYear($this->delivery->birthday_year),
            'scheduledAt' => $this->delivery->scheduled_for?->copy()->timezone($contact?->user?->birthdaySettings?->timezone ?? config('app.timezone')),
            'daysBefore' => $daysBefore,
            'messagePlan' => $messagePlan,
        ]);
    }
}
