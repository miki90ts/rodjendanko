<?php

namespace App\Models;

use App\Enums\DeliveryStatus;
use App\Enums\DeliveryType;
use App\Enums\NotificationChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthdayDelivery extends Model
{
    protected $fillable = ['user_id', 'idempotency_key', 'type', 'channel', 'message_id', 'email_template_id', 'birthday_year', 'occurrence_key', 'scheduled_for', 'sent_at', 'status', 'recipient', 'subject', 'rendered_message', 'provider_message_id', 'error', 'attempts'];

    protected function casts(): array
    {
        return ['type' => DeliveryType::class, 'channel' => NotificationChannel::class, 'status' => DeliveryStatus::class, 'scheduled_for' => 'datetime', 'sent_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(BirthdayContact::class, 'birthday_contact_id');
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(BirthdayMessage::class);
    }

    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }
}
