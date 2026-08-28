<?php

namespace App\Actions;

use App\Enums\DeliveryType;
use App\Enums\LeapDayRule;
use App\Models\BirthdayContact;
use App\Services\BirthdayCalculator;
use Carbon\CarbonInterface;

class ScheduleBirthdayNotificationsAction
{
    public function __construct(private readonly BirthdayCalculator $calculator, private readonly PrepareBirthdayDeliveryAction $prepare) {}

    public function execute(?CarbonInterface $now = null): int
    {
        $now ??= now();
        $created = 0;

        BirthdayContact::query()->where('is_active', true)->with(['user.birthdaySettings.defaultEmailTemplate', 'settings.selectedMessage', 'settings.emailTemplate', 'reminders'])->chunkById(100, function ($contacts) use ($now, &$created): void {
            foreach ($contacts as $contact) {
                try {
                    $created += $this->scheduleContact($contact, $now);
                } catch (\Throwable $exception) {
                    report($exception);
                }
            }
        });

        return $created;
    }

    private function scheduleContact(BirthdayContact $contact, CarbonInterface $now): int
    {
        $created = 0;
        $timezone = $contact->user->birthdaySettings?->timezone ?? config('app.timezone');
        $localNow = $now->copy()->setTimezone($timezone);
        $rule = $contact->user->birthdaySettings?->leap_day_rule ?? LeapDayRule::February28;
        $birthday = $this->calculator->next($contact->birth_date, $localNow, $rule);

        if ($contact->settings?->auto_send_enabled && $contact->email) {
            $due = $birthday->copy()->setTimeFromTimeString($contact->settings->send_time ?? $contact->user->birthdaySettings?->default_send_time ?? '09:00');
            if ($this->isDue($due, $localNow)) {
                $created += (int) $this->prepare->execute($contact, DeliveryType::Greeting, $birthday->year, $due)->wasRecentlyCreated;
            }
        }

        if ($contact->user->birthdaySettings?->reminders_enabled ?? true) {
            foreach ($contact->reminders->where('is_enabled', true) as $reminder) {
                $due = $birthday->copy()->subDays($reminder->days_before)->setTimeFromTimeString($reminder->send_time ?? $contact->user->birthdaySettings?->default_reminder_time ?? '09:00');
                if ($this->isDue($due, $localNow)) {
                    $created += (int) $this->prepare->execute($contact, DeliveryType::Reminder, $birthday->year, $due, $reminder)->wasRecentlyCreated;
                }
            }
        }

        return $created;
    }

    private function isDue(CarbonInterface $scheduled, CarbonInterface $now): bool
    {
        return $scheduled->lte($now) && $scheduled->gte($now->copy()->subDay());
    }
}
