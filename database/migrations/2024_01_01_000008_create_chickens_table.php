<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chickens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chicken_type_id')->constrained()->cascadeOnDelete();
            $table->string('code', 100)->unique();
            $table->string('name')->nullable();
            $table->string('gender')->default('jantan'); // enum: jantan, betina
            $table->date('birth_date')->nullable();
            $table->integer('age_weeks')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->string('status')->default('sehat'); // enum: sehat, sakit, mati, terjual
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('farm_id');
            $table->index('chicken_type_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chickens');
    }
};
