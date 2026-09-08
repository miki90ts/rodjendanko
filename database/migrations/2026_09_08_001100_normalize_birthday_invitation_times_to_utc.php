<?php

use Carbon\CarbonImmutable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('birthday_invitations')
            ->select(['id', 'starts_at', 'ends_at', 'timezone'])
            ->orderBy('id')
            ->each(function (object $invitation): void {
                $timezone = in_array($invitation->timezone, timezone_identifiers_list(), true)
                    ? $invitation->timezone
                    : 'Europe/Belgrade';

                DB::table('birthday_invitations')->where('id', $invitation->id)->update([
                    'starts_at' => CarbonImmutable::parse($invitation->starts_at, $timezone)->utc()->format('Y-m-d H:i:s'),
                    'ends_at' => $invitation->ends_at
                        ? CarbonImmutable::parse($invitation->ends_at, $timezone)->utc()->format('Y-m-d H:i:s')
                        : null,
                ]);
            });
    }

    public function down(): void
    {
        DB::table('birthday_invitations')
            ->select(['id', 'starts_at', 'ends_at', 'timezone'])
            ->orderBy('id')
            ->each(function (object $invitation): void {
                $timezone = in_array($invitation->timezone, timezone_identifiers_list(), true)
                    ? $invitation->timezone
                    : 'Europe/Belgrade';

                DB::table('birthday_invitations')->where('id', $invitation->id)->update([
                    'starts_at' => CarbonImmutable::parse($invitation->starts_at, 'UTC')->setTimezone($timezone)->format('Y-m-d H:i:s'),
                    'ends_at' => $invitation->ends_at
                        ? CarbonImmutable::parse($invitation->ends_at, 'UTC')->setTimezone($timezone)->format('Y-m-d H:i:s')
                        : null,
                ]);
            });
    }
};
