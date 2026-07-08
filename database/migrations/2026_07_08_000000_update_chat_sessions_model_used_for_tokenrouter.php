<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TokenRouter model IDs carry a provider prefix (e.g. openai/gpt-4o-mini),
     * so bare OpenAI names stored by older sessions no longer resolve.
     */
    public function up(): void
    {
        DB::table('chat_sessions')
            ->whereIn('model_used', ['gpt-4o', 'gpt-4o-mini', 'gpt-3.5-turbo'])
            ->update(['model_used' => 'openai/gpt-4o-mini']);

        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->string('model_used', 100)->default('openai/gpt-4o-mini')->change();
        });
    }

    public function down(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->string('model_used', 100)->default('gpt-4o')->change();
        });
    }
};
