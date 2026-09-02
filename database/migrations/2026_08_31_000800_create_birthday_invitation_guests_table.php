<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('birthday_invitation_guests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('birthday_invitation_id')->constrained()->cascadeOnDelete();
            $table->uuid('token')->unique();
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('status', 30)->default('pending');
            $table->unsignedTinyInteger('party_size')->default(1);
            $table->text('message')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['birthday_invitation_id', 'email']);
            $table->index(['birthday_invitation_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birthday_invitation_guests');
    }
};
