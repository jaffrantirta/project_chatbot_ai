<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('confusion_matrix_tests', function (Blueprint $table) {
            $table->id();
            $table->string('test_name');
            $table->foreignId('tested_by')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->integer('total_samples');
            $table->integer('true_positive')->default(0);
            $table->integer('true_negative')->default(0);
            $table->integer('false_positive')->default(0);
            $table->integer('false_negative')->default(0);
            $table->decimal('accuracy', 5, 4)->nullable();
            $table->decimal('precision_score', 5, 4)->nullable();
            $table->decimal('recall_score', 5, 4)->nullable();
            $table->decimal('f1_score', 5, 4)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('tested_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('confusion_matrix_tests');
    }
};
