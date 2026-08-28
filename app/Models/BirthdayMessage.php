<?php

namespace App\Models;

use App\Enums\MessageCategory;
use App\Enums\MessageTone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthdayMessage extends Model
{
    protected $fillable = ['name', 'category', 'tone', 'body', 'is_active'];

    protected function casts(): array
    {
        return ['category' => MessageCategory::class, 'tone' => MessageTone::class, 'is_active' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeAvailableTo(Builder $query, int $userId): Builder
    {
        return $query->where(fn (Builder $q) => $q->whereNull('user_id')->orWhere('user_id', $userId));
    }
}
