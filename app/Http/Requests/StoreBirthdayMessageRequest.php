<?php

namespace App\Http\Requests;

use App\Enums\MessageCategory;
use App\Enums\MessageTone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBirthdayMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:100'],
            'category' => ['required', Rule::enum(MessageCategory::class)],
            'tone' => ['required', Rule::enum(MessageTone::class)],
            'body' => ['required', 'string', 'max:5000'],
        ];
    }
}
