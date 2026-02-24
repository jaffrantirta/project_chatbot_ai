<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chicken_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('chat_session_id')
                ->nullable()
                ->constrained('chat_sessions')
                ->nullOnDelete();
            $table->foreignId('disease_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('sehat'); // enum: sehat, sakit, dalam_pengobatan, sembuh, mati
            $table->text('symptoms_reported')->nullable();
            $table->text('diagnosis_result')->nullable();
            $table->text('treatment_given')->nullable();
            $table->text('medicine_given')->nullable();
            $table->boolean('vet_consulted')->default(false);
            $table->date('record_date');
            $table->date('follow_up_date')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('chicken_id');
            $table->index('farm_id');
            $table->index('disease_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_records');
    }
};
