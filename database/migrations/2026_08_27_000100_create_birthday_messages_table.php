<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('birthday_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('category', 40);
            $table->string('tone', 40);
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'category', 'tone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birthday_messages');
    }
};
