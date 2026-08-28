<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_birthday_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('timezone', 64)->default('Europe/Belgrade');
            $table->boolean('reminders_enabled')->default(true);
            $table->json('default_reminder_days');
            $table->time('default_reminder_time')->default('09:00');
            $table->boolean('auto_send_default')->default(false);
            $table->time('default_send_time')->default('09:00');
            $table->string('default_channel', 20)->default('email');
            $table->string('default_message_mode', 20)->default('random');
            $table->string('default_message_category', 40)->nullable();
            $table->foreignId('default_email_template_id')->nullable()->constrained('email_templates')->nullOnDelete();
            $table->string('leap_day_rule', 20)->default('february_28');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_birthday_settings');
    }
};
