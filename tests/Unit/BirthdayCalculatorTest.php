<?php

namespace Tests\Unit;

use App\Enums\LeapDayRule;
use App\Services\BirthdayCalculator;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BirthdayCalculatorTest extends TestCase
{
    #[DataProvider('nextBirthdayCases')]
    public function test_it_calculates_the_next_annual_birthday(string $birthDate, string $today, string $expected): void
    {
        $result = (new BirthdayCalculator)->next(Carbon::parse($birthDate), Carbon::parse($today));
        $this->assertSame($expected, $result->toDateString());
    }

    public static function nextBirthdayCases(): array
    {
        return [
            'birthday later this year' => ['1990-12-10', '2027-08-10', '2027-12-10'],
            'birthday today' => ['1990-08-10', '2027-08-10 18:00', '2027-08-10'],
            'birthday next year' => ['1990-05-10', '2027-08-10', '2028-05-10'],
        ];
    }

    public function test_february_29_defaults_to_february_28_in_a_non_leap_year(): void
    {
        $result = (new BirthdayCalculator)->occurrence(Carbon::parse('2000-02-29'), 2027);
        $this->assertSame('2027-02-28', $result->toDateString());
    }

    public function test_february_29_can_be_observed_on_march_1(): void
    {
        $result = (new BirthdayCalculator)->occurrence(Carbon::parse('2000-02-29'), 2027, LeapDayRule::March1);
        $this->assertSame('2027-03-01', $result->toDateString());
    }

    public function test_february_29_remains_unchanged_in_a_leap_year(): void
    {
        $result = (new BirthdayCalculator)->occurrence(Carbon::parse('2000-02-29'), 2028);
        $this->assertSame('2028-02-29', $result->toDateString());
    }
}
