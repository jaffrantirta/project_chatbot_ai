<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('chicken_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('session_token')->unique();
            $table->string('status')->default('active'); // enum: active, closed
            $table->integer('total_tokens_used')->default(0);
            $table->string('model_used', 100)->default('gpt-4o');
            $table->timestamps();

            $table->index('user_id');
            $table->index('farm_id');
            $table->index('chicken_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
