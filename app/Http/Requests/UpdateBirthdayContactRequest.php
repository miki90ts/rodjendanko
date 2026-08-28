<?php

namespace App\Http\Requests;

class UpdateBirthdayContactRequest extends StoreBirthdayContactRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('birthdayContact')) ?? false;
    }
}
