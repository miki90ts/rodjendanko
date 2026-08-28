<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthdayReminder extends Model
{
    protected $fillable = ['days_before', 'send_time', 'is_enabled'];

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean'];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(BirthdayContact::class, 'birthday_contact_id');
    }
}
