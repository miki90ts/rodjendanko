<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('birthday_invitations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('public_token')->unique();
            $table->string('title');
            $table->string('honoree_name');
            $table->unsignedSmallInteger('celebrant_age')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->string('timezone', 80)->default('Europe/Belgrade');
            $table->string('venue_name')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('description')->nullable();
            $table->string('dress_code')->nullable();
            $table->string('gift_note')->nullable();
            $table->string('host_name');
            $table->string('host_phone', 40)->nullable();
            $table->date('reply_deadline')->nullable();
            $table->string('template', 30)->default('cheerful');
            $table->string('background_image_path')->nullable();
            $table->boolean('allow_plus_one')->default(false);
            $table->unsignedSmallInteger('max_guests')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birthday_invitations');
    }
};
