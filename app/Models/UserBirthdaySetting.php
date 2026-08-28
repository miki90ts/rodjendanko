<?php

namespace App\Models;

use App\Enums\LeapDayRule;
use App\Enums\MessageCategory;
use App\Enums\MessageMode;
use App\Enums\NotificationChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBirthdaySetting extends Model
{
    protected $attributes = [
        'default_reminder_days' => '[3]',
    ];

    protected $fillable = ['timezone', 'reminders_enabled', 'default_reminder_days', 'default_reminder_time', 'auto_send_default', 'default_send_time', 'default_channel', 'default_message_mode', 'default_message_category', 'default_email_template_id', 'leap_day_rule'];

    protected function casts(): array
    {
        return ['reminders_enabled' => 'boolean', 'default_reminder_days' => 'array', 'auto_send_default' => 'boolean', 'default_channel' => NotificationChannel::class, 'default_message_mode' => MessageMode::class, 'default_message_category' => MessageCategory::class, 'leap_day_rule' => LeapDayRule::class];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function defaultEmailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'default_email_template_id');
    }
}
