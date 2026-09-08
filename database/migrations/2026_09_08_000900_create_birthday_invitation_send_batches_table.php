<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('birthday_invitation_send_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('birthday_invitation_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('attempt_number');
            $table->unsignedSmallInteger('recipient_count');
            $table->unsignedSmallInteger('successful_count')->default(0);
            $table->unsignedSmallInteger('failed_count')->default(0);
            $table->string('status', 20)->default('pending');
            $table->timestamp('queued_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['birthday_invitation_id', 'attempt_number'], 'invitation_send_attempt_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birthday_invitation_send_batches');
    }
};
