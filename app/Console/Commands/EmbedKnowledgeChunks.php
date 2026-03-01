<?php

namespace App\Console\Commands;

use App\Jobs\EmbedKnowledgeChunkJob;
use App\Models\KnowledgeChunk;
use Illuminate\Console\Command;

class EmbedKnowledgeChunks extends Command
{
    protected $signature = 'rag:embed
                            {--chunk= : Only embed a specific chunk ID}
                            {--document= : Only embed chunks for a specific document ID}
                            {--force : Re-embed chunks that are already embedded}';

    protected $description = 'Dispatch embedding jobs for all unembedded knowledge chunks';

    public function handle(): int
    {
        $query = KnowledgeChunk::query();

        if ($id = $this->option('chunk')) {
            $query->where('id', $id);
        } elseif ($docId = $this->option('document')) {
            $query->where('document_id', $docId);
        }

        if (! $this->option('force')) {
            $query->where('is_embedded', false);
        }

        $chunks = $query->get();

        if ($chunks->isEmpty()) {
            $this->info('No chunks to embed.');
            return self::SUCCESS;
        }

        $this->info("Dispatching embedding jobs for {$chunks->count()} chunk(s)…");

        $bar = $this->output->createProgressBar($chunks->count());
        $bar->start();

        foreach ($chunks as $chunk) {
            EmbedKnowledgeChunkJob::dispatch($chunk);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done. Run `php artisan queue:work` if using async queues.');

        return self::SUCCESS;
    }
}
