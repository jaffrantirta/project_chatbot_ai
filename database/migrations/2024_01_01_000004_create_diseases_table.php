<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diseases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disease_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('local_name')->nullable();
            $table->text('cause');
            $table->text('symptoms');
            $table->text('medicine')->nullable();
            $table->text('treatment');
            $table->text('prevention')->nullable();
            $table->string('source')->nullable();
            $table->text('reference_url')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diseases');
    }
};
