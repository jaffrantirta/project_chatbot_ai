<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('knowledge_chunks', function (Blueprint $table) {
            // Store the embedding vector as a JSON array of floats (e.g. 1536-dim)
            $table->json('embedding')->nullable()->after('is_embedded');
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_chunks', function (Blueprint $table) {
            $table->dropColumn('embedding');
        });
    }
};
