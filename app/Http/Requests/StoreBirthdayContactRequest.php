<?php

namespace App\Http\Requests;

use App\Enums\MessageCategory;
use App\Enums\MessageMode;
use App\Enums\MessageTone;
use App\Enums\NotificationChannel;
use App\Enums\RelationshipType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBirthdayContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'email' => ['nullable', 'email:rfc', 'max:255', Rule::requiredIf(fn () => $this->boolean('settings.auto_send_enabled'))],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[+()\-\s0-9]{6,40}$/'],
            'relationship' => ['nullable', Rule::enum(RelationshipType::class)],
            'notes' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['required', 'boolean'],
            'reminders' => ['array', 'max:10'],
            'reminders.*' => ['integer', 'min:0', 'max:365', 'distinct'],
            'settings' => ['required', 'array'],
            'settings.auto_send_enabled' => ['required', 'boolean'],
            'settings.send_time' => ['nullable', 'date_format:H:i'],
            'settings.channel' => ['required', Rule::enum(NotificationChannel::class)],
            'settings.message_mode' => ['required', Rule::enum(MessageMode::class)],
            'settings.message_category' => ['nullable', Rule::enum(MessageCategory::class)],
            'settings.message_tone' => ['nullable', Rule::enum(MessageTone::class)],
            'settings.selected_message_id' => [
                Rule::requiredIf(fn () => $this->boolean('settings.auto_send_enabled') && $this->input('settings.message_mode') === MessageMode::Selected->value),
                'nullable',
                Rule::exists('birthday_messages', 'id')->where(fn ($query) => $query->whereNull('user_id')->orWhere('user_id', $this->user()->id)),
            ],
            'settings.custom_message' => [Rule::requiredIf(fn () => $this->boolean('settings.auto_send_enabled') && $this->input('settings.message_mode') === MessageMode::Custom->value), 'nullable', 'string', 'max:5000'],
            'settings.email_template_id' => ['nullable', Rule::exists('email_templates', 'id')->where('is_active', true)],
            'settings.email_subject' => ['nullable', 'string', 'max:255'],
        ];
    }
}
