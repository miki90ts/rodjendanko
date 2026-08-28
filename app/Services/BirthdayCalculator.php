<?php

namespace App\Services;

use App\Enums\LeapDayRule;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class BirthdayCalculator
{
    public function occurrence(CarbonInterface $birthDate, int $year, LeapDayRule $rule = LeapDayRule::February28, \DateTimeZone|string|null $timezone = null): Carbon
    {
        $month = $birthDate->month;
        $day = $birthDate->day;

        if ($month === 2 && $day === 29 && ! Carbon::create($year, 1, 1)->isLeapYear()) {
            [$month, $day] = $rule === LeapDayRule::March1 ? [3, 1] : [2, 28];
        }

        return Carbon::create($year, $month, $day, 0, 0, 0, $timezone ?? $birthDate->getTimezone())->startOfDay();
    }

    public function next(CarbonInterface $birthDate, ?CarbonInterface $from = null, LeapDayRule $rule = LeapDayRule::February28): Carbon
    {
        $from = Carbon::instance($from ?? now());
        $birthday = $this->occurrence($birthDate, $from->year, $rule, $from->getTimezone());

        return $birthday->lt($from->copy()->startOfDay())
            ? $this->occurrence($birthDate, $from->year + 1, $rule, $from->getTimezone())
            : $birthday;
    }

    public function ageOn(CarbonInterface $birthDate, CarbonInterface $birthday): int
    {
        return $birthday->year - $birthDate->year;
    }
}
