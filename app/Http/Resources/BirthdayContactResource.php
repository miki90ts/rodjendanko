<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BirthdayContactResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        $next = $this->resource->nextBirthday(now($this->user->birthdaySettings?->timezone));

        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'nickname' => $this->nickname,
            'display_name' => $this->display_name,
            'birth_date' => $this->birth_date->format('Y-m-d'),
            'email' => $this->email,
            'phone' => $this->phone,
            'relationship' => $this->relationship,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'next_birthday' => $next->toDateString(),
            'days_until' => now($this->user->birthdaySettings?->timezone)->startOfDay()->diffInDays($next),
            'turning_age' => $next->year - $this->birth_date->year,
            'settings' => $this->whenLoaded('settings'),
            'reminders' => $this->whenLoaded('reminders', fn () => $this->reminders->pluck('days_before')->values()),
        ];
    }
}
