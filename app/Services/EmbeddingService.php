<?php

namespace App\Services;

use App\Models\KnowledgeBaseDocument;
use App\Models\KnowledgeChunk;

class EmbeddingService
{
    /**
     * Max characters per chunk (~375 tokens at 4 chars/token).
     * Keeps each chunk well within the 8192-token embedding limit.
     */
    private const MAX_CHARS = 1500;

    /**
     * Overlap between consecutive chunks to preserve cross-boundary context.
     */
    private const OVERLAP_CHARS = 150;

    /**
     * Minimum cosine similarity to consider a chunk relevant.
     */
    public const SIMILARITY_THRESHOLD = 0.40;

    // ─── Embedding ────────────────────────────────────────────────────────────

    /**
     * Call OpenAI Embeddings API and return the float vector.
     */
    public function getEmbedding(string $text): array
    {
        $client = \OpenAI::factory()
            ->withApiKey(config('services.openai.key'))
            ->withBaseUri(config('services.openai.base_url'))
            ->make();

        $response = $client->embeddings()->create([
            'model' => config('services.openai.embedding_model', 'text-embedding-3-small'),
            'input' => mb_substr($text, 0, 8000), // safety: never exceed API limit
        ]);

        return $response->embeddings[0]->embedding;
    }

    /**
     * Embed a single KnowledgeChunk and persist the vector.
     */
    public function embedChunk(KnowledgeChunk $chunk): void
    {
        $vector = $this->getEmbedding($chunk->content);

        $chunk->update([
            'embedding'    => $vector,
            'is_embedded'  => true,
            'embedding_id' => 'openai-' . $chunk->id,
        ]);
    }

    // ─── Similarity ───────────────────────────────────────────────────────────

    /**
     * Compute cosine similarity between two float vectors.
     * Returns a value between -1.0 and 1.0 (higher = more similar).
     */
    public function cosineSimilarity(array $a, array $b): float
    {
        $dot   = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        $len = min(count($a), count($b));
        for ($i = 0; $i < $len; $i++) {
            $dot   += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        if ($normA === 0.0 || $normB === 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    /**
     * Find the top-K most similar embedded chunks for a query string.
     * Returns chunks sorted by similarity descending, filtered by threshold.
     */
    public function findSimilarChunks(string $query, int $topK = 3): \Illuminate\Database\Eloquent\Collection
    {
        $queryVector = $this->getEmbedding($query);

        // Load all embedded chunks (with their vectors)
        $chunks = KnowledgeChunk::where('is_embedded', true)
            ->whereNotNull('embedding')
            ->get();

        if ($chunks->isEmpty()) {
            return $chunks;
        }

        // Score every chunk
        $scored = $chunks->map(function (KnowledgeChunk $chunk) use ($queryVector) {
            return [
                'chunk'      => $chunk,
                'similarity' => $this->cosineSimilarity($queryVector, $chunk->embedding),
            ];
        });

        // Filter by threshold, then take top-K
        $results = $scored
            ->filter(fn ($item) => $item['similarity'] >= self::SIMILARITY_THRESHOLD)
            ->sortByDesc('similarity')
            ->take($topK)
            ->pluck('chunk');

        // Return as Eloquent Collection
        return KnowledgeChunk::whereIn('id', $results->pluck('id'))->get();
    }

    // ─── Chunking ─────────────────────────────────────────────────────────────

    /**
     * Split raw text into overlapping chunks.
     *
     * Strategy (priority order):
     *   1. Break at paragraph boundary (\n\n)
     *   2. Break at sentence boundary ('. ')
     *   3. Hard-cut at MAX_CHARS
     */
    public function chunkText(string $text): array
    {
        $text = preg_replace('/\r\n|\r/', "\n", $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        $text = trim($text);

        $chunks = [];
        $length = strlen($text);
        $start  = 0;

        while ($start < $length) {
            $end = $start + self::MAX_CHARS;

            if ($end >= $length) {
                $chunk = trim(substr($text, $start));
                if (strlen($chunk) > 20) {
                    $chunks[] = $chunk;
                }
                break;
            }

            $slice = substr($text, $start, $end - $start);

            // 1. Prefer paragraph break
            $breakAt = strrpos($slice, "\n\n");
            if ($breakAt !== false && $breakAt > self::MAX_CHARS * 0.4) {
                $end = $start + $breakAt;
            } else {
                // 2. Prefer sentence break
                $breakAt = strrpos($slice, '. ');
                if ($breakAt !== false && $breakAt > self::MAX_CHARS * 0.4) {
                    $end = $start + $breakAt + 1; // include the dot
                }
                // 3. Otherwise hard-cut (already at $start + MAX_CHARS)
            }

            $chunk = trim(substr($text, $start, $end - $start));
            if (strlen($chunk) > 20) {
                $chunks[] = $chunk;
            }

            // Move forward with overlap
            $start = max($start + 1, $end - self::OVERLAP_CHARS);
        }

        return array_values($chunks);
    }

    /**
     * Delete all existing chunks for a document and re-create them from content.
     * Returns the number of chunks created.
     */
    public function createChunksForDocument(KnowledgeBaseDocument $document): int
    {
        if (blank($document->content)) {
            throw new \RuntimeException('Konten dokumen kosong. Ekstrak teks dari file terlebih dahulu sebelum membuat chunks.');
        }

        $document->chunks()->delete();

        $texts = $this->chunkText($document->content);

        foreach ($texts as $index => $text) {
            KnowledgeChunk::create([
                'document_id' => $document->id,
                'chunk_index' => $index,
                'content'     => $text,
                'token_count' => (int) ceil(mb_strlen($text) / 4), // ~4 chars per token
                'is_embedded' => false,
                'embedding'   => null,
                'embedding_id' => null,
            ]);
        }

        return count($texts);
    }
}
