<?php

namespace Tests\Feature;

use App\Models\BirthdayContact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BirthdayContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_update_and_delete_a_contact(): void
    {
        $user = User::factory()->create();
        $payload = $this->payload();

        $this->actingAs($user)->post(route('birthdays.store'), $payload)->assertRedirect(route('birthdays.index'));
        $contact = $user->birthdayContacts()->firstOrFail();
        $this->assertSame([7, 1, 0], $contact->reminders()->orderByDesc('days_before')->pluck('days_before')->all());
        $this->assertTrue($contact->settings->auto_send_enabled);

        $this->actingAs($user)->put(route('birthdays.update', $contact), [...$payload, 'first_name' => 'Miloš', 'reminders' => [3]])->assertRedirect(route('birthdays.index'));
        $this->assertSame('Miloš', $contact->refresh()->first_name);
        $this->assertSame([3], $contact->reminders()->pluck('days_before')->all());

        $this->actingAs($user)->delete(route('birthdays.destroy', $contact))->assertRedirect(route('birthdays.index'));
        $this->assertModelMissing($contact);
    }

    public function test_user_cannot_view_update_or_delete_another_users_contact(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $contact = BirthdayContact::factory()->for($owner)->create();

        $this->actingAs($intruder)->get(route('birthdays.show', $contact))->assertForbidden();
        $this->actingAs($intruder)->put(route('birthdays.update', $contact), $this->payload())->assertForbidden();
        $this->actingAs($intruder)->delete(route('birthdays.destroy', $contact))->assertForbidden();
    }

    public function test_auto_send_requires_an_email_and_mode_specific_content(): void
    {
        $user = User::factory()->create();
        $payload = $this->payload();
        $payload['email'] = null;
        $payload['settings']['message_mode'] = 'custom';
        $payload['settings']['custom_message'] = null;

        $this->actingAs($user)->post(route('birthdays.store'), $payload)->assertInvalid(['email', 'settings.custom_message']);
    }

    private function payload(): array
    {
        return [
            'first_name' => 'Marko', 'last_name' => 'Marković', 'nickname' => 'Mare', 'birth_date' => '1990-05-12',
            'email' => 'marko@example.com', 'phone' => '+381 64 123 456', 'relationship' => 'friend', 'notes' => 'Voli putovanja', 'is_active' => true,
            'reminders' => [7, 1, 0],
            'settings' => ['auto_send_enabled' => true, 'send_time' => '09:00', 'channel' => 'email', 'message_mode' => 'random', 'message_category' => null, 'message_tone' => null, 'selected_message_id' => null, 'custom_message' => null, 'email_template_id' => null, 'email_subject' => 'Srećan rođendan, {{first_name}}!'],
        ];
    }
}
