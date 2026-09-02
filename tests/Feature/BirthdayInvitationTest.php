<?php

namespace Tests\Feature;

use App\Enums\InvitationTemplate;
use App\Enums\RsvpStatus;
use App\Jobs\SendBirthdayInvitationEmail;
use App\Mail\BirthdayInvitationMail;
use App\Models\BirthdayInvitation;
use App\Models\BirthdayInvitationGuest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class BirthdayInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_an_invitation_for_multiple_guests_and_queue_emails(): void
    {
        Bus::fake();
        $user = User::factory()->create(['name' => 'Milan']);

        $response = $this->actingAs($user)->post(route('invitations.store'), [
            ...$this->payload(),
            'send_now' => true,
            'guests' => [
                ['name' => 'Ana', 'email' => 'ANA@example.com'],
                ['name' => 'Petar', 'email' => 'petar@example.com'],
            ],
        ]);

        $invitation = $user->birthdayInvitations()->firstOrFail();
        $response->assertRedirect(route('invitations.show', $invitation));
        $this->assertSame(2, $invitation->guests()->count());
        $this->assertSame('ana@example.com', $invitation->guests()->orderBy('id')->value('email'));
        $this->assertSame(0, $invitation->guests()->whereNotNull('sent_at')->count());
        Bus::assertDispatched(SendBirthdayInvitationEmail::class, 2);
    }

    public function test_other_user_cannot_open_change_send_or_delete_an_invitation(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $invitation = $this->invitation($owner);

        $this->actingAs($intruder)->get(route('invitations.show', $invitation))->assertForbidden();
        $this->actingAs($intruder)->put(route('invitations.update', $invitation), $this->payload())->assertForbidden();
        $this->actingAs($intruder)->post(route('invitations.send', $invitation))->assertForbidden();
        $this->actingAs($intruder)->delete(route('invitations.destroy', $invitation))->assertForbidden();
    }

    public function test_published_invitation_is_public_but_a_draft_is_not(): void
    {
        $invitation = $this->invitation(User::factory()->create());

        $this->get(route('invitations.public', $invitation->public_token))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('invitations/Public')
                ->where('invitation.title', 'Lenin sedmi rođendan'));

        $invitation->update(['is_published' => false]);
        $this->get(route('invitations.public', $invitation->public_token))->assertNotFound();
    }

    public function test_invited_guest_can_confirm_attendance_with_a_plus_one(): void
    {
        $invitation = $this->invitation(User::factory()->create(), ['allow_plus_one' => true]);
        $guest = $this->guest($invitation);

        $this->post(route('invitations.rsvp', [$invitation->public_token, $guest->token]), [
            'status' => RsvpStatus::Attending->value,
            'party_size' => 2,
            'message' => 'Radujemo se proslavi!',
        ])->assertRedirect();

        $guest->refresh();
        $this->assertSame(RsvpStatus::Attending, $guest->status);
        $this->assertSame(2, $guest->party_size);
        $this->assertSame('Radujemo se proslavi!', $guest->message);
        $this->assertNotNull($guest->responded_at);
    }

    public function test_draft_cannot_be_emailed_and_rsvp_deadline_is_enforced(): void
    {
        Bus::fake();
        $user = User::factory()->create();
        $draft = $this->invitation($user, ['is_published' => false]);
        $this->guest($draft);

        $this->actingAs($user)->post(route('invitations.send', $draft))
            ->assertInvalid(['send']);
        Bus::assertNotDispatched(SendBirthdayInvitationEmail::class);

        $expired = $this->invitation($user, ['reply_deadline' => now()->subDay()->toDateString()]);
        $guest = $this->guest($expired, ['email' => 'kasno@example.com']);
        $this->post(route('invitations.rsvp', [$expired->public_token, $guest->token]), [
            'status' => RsvpStatus::Attending->value,
            'party_size' => 1,
        ])->assertInvalid(['status']);
        $this->assertSame(RsvpStatus::Pending, $guest->refresh()->status);
    }

    public function test_invitation_capacity_counts_plus_ones(): void
    {
        $invitation = $this->invitation(User::factory()->create(), [
            'allow_plus_one' => true,
            'max_guests' => 2,
        ]);
        $this->guest($invitation, [
            'email' => 'potvrdjen@example.com',
            'status' => RsvpStatus::Attending,
            'party_size' => 2,
        ]);
        $waitingGuest = $this->guest($invitation, ['email' => 'ceka@example.com']);

        $this->post(route('invitations.rsvp', [$invitation->public_token, $waitingGuest->token]), [
            'status' => RsvpStatus::Attending->value,
            'party_size' => 1,
        ])->assertInvalid(['party_size']);
        $this->assertSame(RsvpStatus::Pending, $waitingGuest->refresh()->status);
    }

    public function test_invitation_email_contains_personalized_rsvp_links_and_event_details(): void
    {
        $invitation = $this->invitation(User::factory()->create());
        $guest = $this->guest($invitation, ['name' => 'Ana']);

        $html = (new BirthdayInvitationMail($invitation, $guest))->render();

        $this->assertStringContainsString('Ana', $html);
        $this->assertStringContainsString('Lenin sedmi rođendan', $html);
        $this->assertStringContainsString('Igraonica Zvezdica', $html);
        $this->assertStringContainsString('Potvrdi dolazak', $html);
        $this->assertStringContainsString('Ne mogu da dođem', $html);
        $this->assertStringContainsString($guest->token, $html);
        $this->assertStringContainsString('google.com/maps', $html);
    }

    public function test_queued_invitation_job_marks_guest_as_sent_only_after_success(): void
    {
        Mail::fake();
        $invitation = $this->invitation(User::factory()->create());
        $guest = $this->guest($invitation);

        $this->assertNull($guest->sent_at);
        (new SendBirthdayInvitationEmail($guest->id))->handle();

        Mail::assertSent(BirthdayInvitationMail::class, fn ($mail) => $mail->guest->is($guest));
        $this->assertNotNull($guest->refresh()->sent_at);
    }

    private function invitation(User $user, array $attributes = []): BirthdayInvitation
    {
        return $user->birthdayInvitations()->create([
            'public_token' => (string) Str::uuid(),
            'title' => 'Lenin sedmi rođendan',
            'honoree_name' => 'Lena',
            'celebrant_age' => 7,
            'starts_at' => now()->addMonth()->setTime(17, 0),
            'ends_at' => now()->addMonth()->setTime(20, 0),
            'timezone' => 'Europe/Belgrade',
            'venue_name' => 'Igraonica Zvezdica',
            'address' => 'Knez Mihailova 10',
            'city' => 'Beograd',
            'latitude' => 44.8176,
            'longitude' => 20.4633,
            'description' => 'Dođite da zajedno ugasimo svećice!',
            'host_name' => 'Milan',
            'reply_deadline' => now()->addWeeks(2)->toDateString(),
            'template' => InvitationTemplate::Kids,
            'allow_plus_one' => false,
            'is_published' => true,
            ...$attributes,
        ]);
    }

    private function guest(BirthdayInvitation $invitation, array $attributes = []): BirthdayInvitationGuest
    {
        return $invitation->guests()->create([
            'token' => (string) Str::uuid(),
            'name' => 'Marko',
            'email' => 'marko@example.com',
            ...$attributes,
        ]);
    }

    private function payload(): array
    {
        return [
            'title' => 'Lenin sedmi rođendan',
            'honoree_name' => 'Lena',
            'celebrant_age' => 7,
            'starts_at' => now()->addMonth()->format('Y-m-d H:i:s'),
            'ends_at' => now()->addMonth()->addHours(3)->format('Y-m-d H:i:s'),
            'timezone' => 'Europe/Belgrade',
            'venue_name' => 'Igraonica Zvezdica',
            'address' => 'Knez Mihailova 10',
            'city' => 'Beograd',
            'latitude' => 44.8176,
            'longitude' => 20.4633,
            'description' => 'Dođite da zajedno ugasimo svećice!',
            'dress_code' => 'Šareno i veselo',
            'gift_note' => 'Najvažnije je da dođete.',
            'host_name' => 'Milan',
            'host_phone' => '+381641234567',
            'reply_deadline' => now()->addWeeks(2)->toDateString(),
            'template' => InvitationTemplate::Kids->value,
            'allow_plus_one' => true,
            'max_guests' => 40,
            'is_published' => true,
            'send_now' => false,
            'guests' => [['name' => 'Ana', 'email' => 'ana@example.com']],
        ];
    }
}
