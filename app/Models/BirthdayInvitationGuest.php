<?php

namespace App\Models;

use App\Enums\RsvpStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthdayInvitationGuest extends Model
{
    protected $fillable = [
        'token',
        'name',
        'email',
        'status',
        'party_size',
        'message',
        'responded_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => RsvpStatus::class,
            'party_size' => 'integer',
            'responded_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(BirthdayInvitation::class, 'birthday_invitation_id');
    }
}
