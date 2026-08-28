<?php

namespace App\Http\Requests;

use App\Enums\LeapDayRule;
use App\Enums\MessageCategory;
use App\Enums\MessageMode;
use App\Enums\NotificationChannel;
use DateTimeZone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBirthdaySettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'timezone' => ['required', Rule::in(DateTimeZone::listIdentifiers())],
            'reminders_enabled' => ['required', 'boolean'],
            'default_reminder_days' => ['required', 'array', 'max:10'],
            'default_reminder_days.*' => ['integer', 'min:0', 'max:365', 'distinct'],
            'default_reminder_time' => ['required', 'date_format:H:i'],
            'auto_send_default' => ['required', 'boolean'],
            'default_send_time' => ['required', 'date_format:H:i'],
            'default_channel' => ['required', Rule::enum(NotificationChannel::class)],
            'default_message_mode' => ['required', Rule::enum(MessageMode::class)],
            'default_message_category' => ['nullable', Rule::enum(MessageCategory::class)],
            'default_email_template_id' => ['nullable', Rule::exists('email_templates', 'id')->where('is_active', true)],
            'leap_day_rule' => ['required', Rule::enum(LeapDayRule::class)],
        ];
    }
}
