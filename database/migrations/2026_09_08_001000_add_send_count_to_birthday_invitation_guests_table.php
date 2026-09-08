<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('birthday_invitation_guests', function (Blueprint $table): void {
            $table->unsignedTinyInteger('send_count')->default(0)->after('sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('birthday_invitation_guests', function (Blueprint $table): void {
            $table->dropColumn('send_count');
        });
    }
};
