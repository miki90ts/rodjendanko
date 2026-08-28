<?php

namespace App\Models;

use App\Enums\LeapDayRule;
use App\Enums\RelationshipType;
use App\Services\BirthdayCalculator;
use Carbon\CarbonInterface;
use Database\Factories\BirthdayContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class BirthdayContact extends Model
{
    /** @use HasFactory<BirthdayContactFactory> */
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'nickname', 'birth_date', 'email', 'phone', 'relationship', 'notes', 'is_active'];

    protected function casts(): array
    {
        return ['birth_date' => 'date', 'relationship' => RelationshipType::class, 'is_active' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(BirthdayContactSetting::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(BirthdayReminder::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(BirthdayDelivery::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->nickname ?: trim($this->first_name.' '.($this->last_name ?? ''));
    }

    public function nextBirthday(?CarbonInterface $from = null): Carbon
    {
        $rule = $this->user->birthdaySettings?->leap_day_rule ?? LeapDayRule::February28;

        return app(BirthdayCalculator::class)->next($this->birth_date, $from, $rule);
    }
}
