<?php

namespace App\Models;

use App\Enums\MessageCategory;
use App\Enums\MessageMode;
use App\Enums\MessageTone;
use App\Enums\NotificationChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthdayContactSetting extends Model
{
    protected $fillable = ['auto_send_enabled', 'send_time', 'channel', 'message_mode', 'message_category', 'message_tone', 'selected_message_id', 'custom_message', 'email_template_id', 'email_subject'];

    protected function casts(): array
    {
        return ['auto_send_enabled' => 'boolean', 'channel' => NotificationChannel::class, 'message_mode' => MessageMode::class, 'message_category' => MessageCategory::class, 'message_tone' => MessageTone::class];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(BirthdayContact::class, 'birthday_contact_id');
    }

    public function selectedMessage(): BelongsTo
    {
        return $this->belongsTo(BirthdayMessage::class, 'selected_message_id');
    }

    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }
}
