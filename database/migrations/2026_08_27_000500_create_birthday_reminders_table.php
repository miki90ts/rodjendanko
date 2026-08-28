<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('birthday_reminders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('birthday_contact_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('days_before');
            $table->time('send_time')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->unique(['birthday_contact_id', 'days_before']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birthday_reminders');
    }
};
