<?php

namespace App\Http\Requests;

use App\Enums\InvitationTemplate;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreBirthdayInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $timezone = (string) $this->input('timezone', 'Europe/Belgrade');
        $validTimezone = in_array($timezone, timezone_identifiers_list(), true);
        $toUtc = static function (mixed $value) use ($timezone, $validTimezone): mixed {
            if (! $value || ! $validTimezone) {
                return $value;
            }

            return CarbonImmutable::parse((string) $value, $timezone)
                ->utc()
                ->format('Y-m-d H:i:s');
        };

        $this->merge([
            'starts_at' => $toUtc($this->input('starts_at')),
            'ends_at' => $toUtc($this->input('ends_at')),
            'allow_plus_one' => $this->boolean('allow_plus_one'),
            'is_published' => $this->boolean('is_published'),
            'send_now' => $this->boolean('send_now'),
            'guests' => collect($this->input('guests', []))
                ->map(fn ($guest) => [
                    'name' => trim((string) ($guest['name'] ?? '')) ?: null,
                    'email' => mb_strtolower(trim((string) ($guest['email'] ?? ''))),
                ])->values()->all(),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'honoree_name' => ['required', 'string', 'max:120'],
            'celebrant_age' => ['nullable', 'integer', 'min:1', 'max:150'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'timezone' => ['required', 'timezone:all'],
            'venue_name' => ['required', 'string', 'max:160'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'description' => ['nullable', 'string', 'max:5000'],
            'dress_code' => ['nullable', 'string', 'max:160'],
            'gift_note' => ['nullable', 'string', 'max:255'],
            'host_name' => ['required', 'string', 'max:120'],
            'host_phone' => ['nullable', 'string', 'max:40'],
            'reply_deadline' => ['nullable', 'date', 'before_or_equal:starts_at'],
            'template' => ['required', Rule::enum(InvitationTemplate::class)],
            'background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'allow_plus_one' => ['required', 'boolean'],
            'max_guests' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'is_published' => ['required', 'boolean'],
            'send_now' => ['required', 'boolean'],
            'guests' => ['required', 'array', 'min:1', 'max:300'],
            'guests.*.name' => ['nullable', 'string', 'max:120'],
            'guests.*.email' => ['required', 'email:rfc', 'max:255', 'distinct:ignore_case'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($this->boolean('send_now') && ! $this->boolean('is_published')) {
                $validator->errors()->add('send_now', 'Pozivnica mora biti javno aktivna pre slanja emailova.');
            }
        }];
    }
}
