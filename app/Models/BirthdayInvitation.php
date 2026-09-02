<?php

namespace App\Models;

use App\Enums\InvitationTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BirthdayInvitation extends Model
{
    protected $fillable = [
        'public_token',
        'title',
        'honoree_name',
        'celebrant_age',
        'starts_at',
        'ends_at',
        'timezone',
        'venue_name',
        'address',
        'city',
        'latitude',
        'longitude',
        'description',
        'dress_code',
        'gift_note',
        'host_name',
        'host_phone',
        'reply_deadline',
        'template',
        'background_image_path',
        'allow_plus_one',
        'max_guests',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'reply_deadline' => 'date',
            'template' => InvitationTemplate::class,
            'latitude' => 'float',
            'longitude' => 'float',
            'allow_plus_one' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(BirthdayInvitationGuest::class);
    }
}
