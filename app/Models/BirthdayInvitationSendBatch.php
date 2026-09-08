<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthdayInvitationSendBatch extends Model
{
    protected $fillable = [
        'attempt_number',
        'recipient_count',
        'successful_count',
        'failed_count',
        'status',
        'queued_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'attempt_number' => 'integer',
            'recipient_count' => 'integer',
            'successful_count' => 'integer',
            'failed_count' => 'integer',
            'queued_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(BirthdayInvitation::class, 'birthday_invitation_id');
    }
}
