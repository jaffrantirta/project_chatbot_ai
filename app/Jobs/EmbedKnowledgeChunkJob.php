<?php

namespace App\Jobs;

use App\Models\KnowledgeChunk;
use App\Services\EmbeddingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EmbedKnowledgeChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Retry up to 3 times on failure, with 30-second backoff.
     */
    public int $tries   = 3;
    public int $backoff = 30;

    public function __construct(
        public readonly KnowledgeChunk $chunk
    ) {}

    public function handle(EmbeddingService $service): void
    {
        // Skip if already embedded (e.g. duplicate dispatch)
        if ($this->chunk->is_embedded) {
            return;
        }

        $service->embedChunk($this->chunk);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('EmbedKnowledgeChunkJob failed', [
            'chunk_id' => $this->chunk->id,
            'error'    => $e->getMessage(),
        ]);
    }
}
