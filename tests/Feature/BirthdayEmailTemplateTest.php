<?php

namespace Tests\Feature;

use App\Enums\DeliveryStatus;
use App\Enums\DeliveryType;
use App\Enums\MessageMode;
use App\Enums\NotificationChannel;
use App\Mail\BirthdayDeliveryMail;
use App\Models\BirthdayContact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BirthdayEmailTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_greeting_email_uses_message_card_and_sender_signature(): void
    {
        [$user, $contact] = $this->contact(MessageMode::Custom);
        $delivery = $contact->deliveries()->create([
            'idempotency_key' => (string) Str::uuid(),
            'user_id' => $user->id,
            'type' => DeliveryType::Greeting,
            'channel' => NotificationChannel::Email,
            'birthday_year' => 2026,
            'occurrence_key' => 'greeting',
            'scheduled_for' => now(),
            'status' => DeliveryStatus::Pending,
            'recipient' => $contact->email,
            'rendered_message' => 'Neka ti svaki dan donese razlog za osmeh!',
        ]);

        $html = (new BirthdayDeliveryMail($delivery->fresh(['contact.user', 'contact.settings'])))->render();

        $this->assertStringContainsString('Srećan rođendan!', $html);
        $this->assertStringContainsString('Neka ti svaki dan donese razlog za osmeh!', $html);
        $this->assertStringContainsString('Sa puno lepih želja,', $html);
        $this->assertStringContainsString('Milan', $html);
        $this->assertStringNotContainsString('Pogledaj čestitku online', $html);
    }

    public function test_reminder_email_shows_message_plan_without_action_buttons(): void
    {
        [$user, $contact] = $this->contact(MessageMode::Selected);
        $delivery = $contact->deliveries()->create([
            'idempotency_key' => (string) Str::uuid(),
            'user_id' => $user->id,
            'type' => DeliveryType::Reminder,
            'channel' => NotificationChannel::Email,
            'birthday_year' => 2026,
            'occurrence_key' => 'reminder:3',
            'scheduled_for' => now()->setTime(9, 0),
            'status' => DeliveryStatus::Pending,
            'recipient' => $user->email,
            'rendered_message' => 'Za 3 dana je rođendan za Anu.',
        ]);

        $html = (new BirthdayDeliveryMail($delivery->fresh(['contact.user.birthdaySettings', 'contact.settings'])))->render();

        $this->assertStringContainsString('Izabrana automatska poruka', $html);
        $this->assertStringContainsString('Izabrana čestitka biće poslata automatski.', $html);
        $this->assertStringContainsString('3 dana ranije u 09:00', $html);
        $this->assertStringNotContainsString('Pošalji čestitku', $html);
        $this->assertStringNotContainsString('Izmeni podsetnik', $html);
    }

    private function contact(MessageMode $mode): array
    {
        $user = User::factory()->create(['name' => 'Milan']);
        $user->birthdaySettings()->create(['timezone' => 'UTC', 'default_reminder_days' => [3]]);
        $contact = BirthdayContact::factory()->for($user)->create([
            'first_name' => 'Ana',
            'birth_date' => '1995-09-15',
            'email' => 'ana@example.com',
        ]);
        $contact->settings()->create([
            'auto_send_enabled' => true,
            'send_time' => '09:00',
            'channel' => NotificationChannel::Email,
            'message_mode' => $mode,
        ]);

        return [$user, $contact];
    }
}
