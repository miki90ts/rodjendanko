<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('birthday_contact_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('birthday_contact_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('auto_send_enabled')->default(false);
            $table->time('send_time')->nullable();
            $table->string('channel', 20)->default('email');
            $table->string('message_mode', 20)->default('random');
            $table->string('message_category', 40)->nullable();
            $table->string('message_tone', 40)->nullable();
            $table->foreignId('selected_message_id')->nullable()->constrained('birthday_messages')->nullOnDelete();
            $table->text('custom_message')->nullable();
            $table->foreignId('email_template_id')->nullable()->constrained('email_templates')->nullOnDelete();
            $table->string('email_subject')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birthday_contact_settings');
    }
};
