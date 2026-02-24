<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')
                ->constrained('chat_sessions')
                ->cascadeOnDelete();
            $table->string('role')->default('user'); // enum: user, assistant, system
            $table->text('content');
            $table->integer('tokens_used')->nullable();
            $table->json('retrieved_chunk_ids')->nullable();
            $table->foreignId('disease_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('session_id');
            $table->index('disease_id');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
