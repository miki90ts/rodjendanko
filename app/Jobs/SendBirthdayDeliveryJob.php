<?php

namespace App\Jobs;

use App\Enums\DeliveryStatus;
use App\Models\BirthdayDelivery;
use App\Services\BirthdayChannelManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SendBirthdayDeliveryJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300, 900];

    public function __construct(public readonly int $deliveryId) {}

    public function handle(BirthdayChannelManager $channels): void
    {
        $delivery = BirthdayDelivery::query()->with([
            'contact.user.birthdaySettings',
            'contact.settings',
            'emailTemplate',
        ])->findOrFail($this->deliveryId);

        if ($delivery->status === DeliveryStatus::Sent) {
            return;
        }

        $claimed = BirthdayDelivery::query()
            ->whereKey($delivery->id)
            ->whereIn('status', [DeliveryStatus::Pending->value, DeliveryStatus::Failed->value])
            ->update(['status' => DeliveryStatus::Processing->value, 'attempts' => $delivery->attempts + 1, 'error' => null]);

        if (! $claimed) {
            return;
        }

        try {
            $providerId = $channels->for($delivery->channel)->send($delivery);
            $delivery->update(['status' => DeliveryStatus::Sent, 'sent_at' => now(), 'provider_message_id' => $providerId]);
        } catch (Throwable $exception) {
            $delivery->update(['status' => DeliveryStatus::Failed, 'error' => mb_substr($exception->getMessage(), 0, 60000)]);
            throw $exception;
        }
    }
}
