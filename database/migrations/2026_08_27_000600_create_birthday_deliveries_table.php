<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('birthday_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->uuid('idempotency_key')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('birthday_contact_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('channel', 20);
            $table->foreignId('message_id')->nullable()->constrained('birthday_messages')->nullOnDelete();
            $table->foreignId('email_template_id')->nullable()->constrained('email_templates')->nullOnDelete();
            $table->unsignedSmallInteger('birthday_year');
            $table->string('occurrence_key', 80);
            $table->timestamp('scheduled_for');
            $table->timestamp('sent_at')->nullable();
            $table->string('status', 20)->default('pending');
            $table->string('recipient');
            $table->string('subject')->nullable();
            $table->text('rendered_message')->nullable();
            $table->string('provider_message_id')->nullable();
            $table->text('error')->nullable();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestamps();

            $table->unique(
                ['birthday_contact_id', 'type', 'birthday_year', 'occurrence_key'],
                'birthday_delivery_once',
            );
            $table->index(['status', 'scheduled_for']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birthday_deliveries');
    }
};
