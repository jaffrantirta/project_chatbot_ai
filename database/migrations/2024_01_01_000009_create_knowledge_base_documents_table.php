<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_base_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->default('pdf'); // enum: pdf, manual, jurnal, web
            $table->longText('content');
            $table->string('file_path')->nullable();
            $table->text('source_url')->nullable();
            $table->boolean('is_processed')->default(false)->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_base_documents');
    }
};
