<?php

namespace Tests\Feature;

use App\Actions\ScheduleBirthdayNotificationsAction;
use App\Contracts\BirthdayMessageChannel;
use App\Enums\DeliveryStatus;
use App\Enums\DeliveryType;
use App\Jobs\SendBirthdayDeliveryJob;
use App\Mail\BirthdayDeliveryMail;
use App\Models\BirthdayContact;
use App\Models\BirthdayMessage;
use App\Models\User;
use App\Services\BirthdayChannelManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BirthdaySchedulingTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_scheduler_creates_greeting_and_multiple_reminders_once(): void
    {
        Bus::fake();
        Carbon::setTestNow('2027-05-12 09:00:00');
        [$user, $contact] = $this->contact();
        $contact->reminders()->createMany([['days_before' => 0], ['days_before' => 7]]);

        $scheduler = app(ScheduleBirthdayNotificationsAction::class);
        $scheduler->execute(now());
        $scheduler->execute(now());

        $this->assertDatabaseCount('birthday_deliveries', 2);
        $this->assertDatabaseHas('birthday_deliveries', ['type' => DeliveryType::Greeting->value, 'birthday_year' => 2027]);
        $this->assertDatabaseHas('birthday_deliveries', ['type' => DeliveryType::Reminder->value, 'birthday_year' => 2027]);
        Bus::assertDispatched(SendBirthdayDeliveryJob::class, 2);
    }

    public function test_manual_or_disabled_contacts_are_not_scheduled(): void
    {
        Bus::fake();
        Carbon::setTestNow('2027-05-12 09:00:00');
        [, $manual] = $this->contact(false);
        [, $disabled] = $this->contact(true, false);

        app(ScheduleBirthdayNotificationsAction::class)->execute(now());
        $this->assertDatabaseCount('birthday_deliveries', 0);
        Bus::assertNothingDispatched();
    }

    public function test_queued_job_sends_and_records_a_successful_delivery(): void
    {
        Mail::fake();
        Bus::fake();
        Carbon::setTestNow('2027-05-12 09:00:00');
        [, $contact] = $this->contact();
        app(ScheduleBirthdayNotificationsAction::class)->execute(now());
        $delivery = $contact->deliveries()->firstOrFail();

        (new SendBirthdayDeliveryJob($delivery->id))->handle(app(\App\Services\BirthdayChannelManager::class));

        Mail::assertSent(BirthdayDeliveryMail::class);
        $this->assertSame(DeliveryStatus::Sent, $delivery->refresh()->status);
        $this->assertNotNull($delivery->sent_at);
    }

    public function test_a_provider_failure_is_recorded_and_can_be_retried(): void
    {
        Bus::fake();
        Carbon::setTestNow('2027-05-12 09:00:00');
        [, $contact] = $this->contact();
        app(ScheduleBirthdayNotificationsAction::class)->execute(now());
        $delivery = $contact->deliveries()->firstOrFail();
        $channel = $this->mock(BirthdayMessageChannel::class);
        $channel->shouldReceive('send')->once()->andThrow(new \RuntimeException('Provider unavailable'));
        $manager = $this->mock(BirthdayChannelManager::class);
        $manager->shouldReceive('for')->once()->andReturn($channel);

        try {
            (new SendBirthdayDeliveryJob($delivery->id))->handle($manager);
            $this->fail('The provider exception was not thrown.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Provider unavailable', $exception->getMessage());
        }

        $this->assertSame(DeliveryStatus::Failed, $delivery->refresh()->status);
        $this->assertSame('Provider unavailable', $delivery->error);

        $this->actingAs($delivery->user)->post(route('deliveries.retry', $delivery))->assertRedirect();
        $this->assertSame(DeliveryStatus::Pending, $delivery->refresh()->status);
    }

    public function test_random_selection_does_not_immediately_repeat_the_last_message(): void
    {
        [$user, $contact] = $this->contact();
        $first = BirthdayMessage::query()->firstOrFail();
        $second = BirthdayMessage::query()->create(['category' => 'funny', 'tone' => 'funny', 'body' => 'Druga poruka', 'is_active' => true]);
        $contact->deliveries()->create(['idempotency_key' => fake()->uuid(), 'user_id' => $user->id, 'type' => 'greeting', 'channel' => 'email', 'message_id' => $first->id, 'birthday_year' => 2026, 'occurrence_key' => 'greeting', 'scheduled_for' => now(), 'sent_at' => now(), 'status' => 'sent', 'recipient' => $contact->email]);

        $selected = app(\App\Services\BirthdayMessageSelector::class)->select($contact->fresh(['settings', 'user']));
        $this->assertSame($second->id, $selected->id);
    }

    private function contact(bool $autoSend = true, bool $active = true): array
    {
        $user = User::factory()->create();
        $user->birthdaySettings()->create(['timezone' => 'UTC', 'default_reminder_days' => [3]]);
        $message = BirthdayMessage::query()->create(['category' => 'funny', 'tone' => 'funny', 'body' => 'Srećan rođendan, {{first_name}}!', 'is_active' => true]);
        $contact = BirthdayContact::factory()->for($user)->create(['first_name' => 'Marko', 'birth_date' => '1990-05-12', 'email' => 'marko@example.com', 'is_active' => $active]);
        $contact->settings()->create(['auto_send_enabled' => $autoSend, 'send_time' => '09:00', 'channel' => 'email', 'message_mode' => 'random', 'message_category' => 'funny']);
        return [$user, $contact->fresh(['user.birthdaySettings', 'settings', 'reminders'])];
    }
}
