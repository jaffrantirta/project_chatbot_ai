<?php

namespace App\Services;

use App\Enums\MessageRole;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\KnowledgeChunk;
use App\Models\SystemPrompt;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use OpenAI\Client;

class ChatService
{
    private Client $client;
    private EmbeddingService $embedding;

    public function __construct(EmbeddingService $embedding)
    {
        $this->embedding = $embedding;
        $this->client    = \OpenAI::factory()
            ->withApiKey(config('services.openai.key'))
            ->withBaseUri(config('services.openai.base_url'))
            ->make();
    }

    /**
     * Send a user message and get an AI response.
     * Returns the saved assistant ChatMessage.
     */
    public function sendMessage(ChatSession $session, string $userContent): ChatMessage
    {
        // 1. Persist user message
        ChatMessage::create([
            'session_id' => $session->id,
            'role'       => MessageRole::User,
            'content'    => $userContent,
        ]);

        // 2. Retrieve relevant knowledge chunks (vector RAG, with fallback)
        $chunks = $this->retrieveChunks($userContent);

        // 3. Build the full messages payload
        $history = $session->messages()->orderBy('created_at')->get();
        $payload = $this->buildPayload($session, $chunks, $history);

        // 4. Call the AI
        $response = $this->client->chat()->create([
            'model'       => $session->model_used ?? config('services.openai.model', 'openai/gpt-4o-mini'),
            'messages'    => $payload,
            'max_tokens'  => 1500,
            'temperature' => 0.3,
        ]);

        $assistantContent = $response->choices[0]->message->content ?? '';
        $tokensUsed       = $response->usage->totalTokens ?? 0;

        // 5. Persist assistant message
        $assistantMessage = ChatMessage::create([
            'session_id'          => $session->id,
            'role'                => MessageRole::Assistant,
            'content'             => $assistantContent,
            'tokens_used'         => $tokensUsed,
            'retrieved_chunk_ids' => $chunks->pluck('id')->toArray(),
        ]);

        // 6. Update running token total on the session
        $session->increment('total_tokens_used', $tokensUsed);

        return $assistantMessage;
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * Vector-similarity retrieval with graceful fallback.
     * Returns empty collection (without API call) if no chunks are embedded.
     * Returns empty collection if the embedding API fails.
     */
    private function retrieveChunks(string $query): Collection
    {
        try {
            return $this->embedding->findSimilarChunks($query, topK: 3);
        } catch (\Throwable $e) {
            Log::warning('RAG retrieval failed — proceeding without knowledge context', [
                'error' => $e->getMessage(),
            ]);
            return KnowledgeChunk::whereNull('id')->get();
        }
    }

    /**
     * Build the OpenAI messages array.
     *
     * Structure:
     *   [system]    Active system prompt + farm/chicken context + RAG chunks
     *   [user]      Message 1
     *   [assistant] Response 1
     *   …
     *   [user]      Current message (already saved to DB)
     */
    private function buildPayload(
        ChatSession $session,
        Collection  $chunks,
        Collection  $history
    ): array {
        // ── System prompt ────────────────────────────────────────────────
        $systemContent = SystemPrompt::where('is_active', true)
            ->orderByDesc('updated_at')
            ->value('content');

        if (blank($systemContent)) {
            $systemContent =
                "Kamu adalah asisten AI khusus monitoring kesehatan ayam.\n" .
                "Jawablah dengan ramah dan jelas, HANYA berdasarkan referensi knowledge base yang diberikan.\n" .
                "Gunakan format Markdown (bold, bullet, heading) agar mudah dibaca.\n" .
                "Jika tidak yakin, sarankan konsultasi dokter hewan.";
        }

        // ── Session context (farm / chicken) ─────────────────────────────
        $session->loadMissing(['farm', 'chicken']);
        $contextLines = [];
        if ($session->farm) {
            $contextLines[] = "Nama kandang/farm: {$session->farm->name}";
        }
        if ($session->chicken) {
            $contextLines[] = "Kode ayam yang dibahas: {$session->chicken->code}";
        }
        if ($contextLines) {
            $systemContent .= "\n\n**Konteks sesi ini:**\n" . implode("\n", $contextLines);
        }

        // ── RAG context ──────────────────────────────────────────────────
        if ($chunks->isNotEmpty()) {
            $systemContent .= "\n\n---\n**Referensi knowledge base:**\n";
            foreach ($chunks as $i => $chunk) {
                $source = $chunk->document?->title ?? 'Dokumen';
                $systemContent .= "\n[{$i}] *{$source}*\n{$chunk->content}\n";
            }
            $systemContent .= "\n---";
            $systemContent .=
                "\n\n**Aturan wajib menjawab:**\n" .
                "1. Jawab HANYA berdasarkan referensi knowledge base di atas. Jangan menambahkan informasi dari pengetahuan umum di luar referensi.\n" .
                "2. Jika referensi di atas TIDAK memuat jawaban atas pertanyaan pengguna, jangan menebak. " .
                "Minta maaf dengan ramah, contohnya: \"Mohon maaf, saya belum memiliki informasi tentang hal itu di basis pengetahuan saya. " .
                "Silakan tanyakan hal lain seputar kesehatan ayam, atau konsultasikan dengan dokter hewan ya 🙏\"\n" .
                "3. Sapaan atau basa-basi ringan (halo, terima kasih, dll.) boleh dibalas dengan ramah tanpa referensi.";
        } else {
            $systemContent .=
                "\n\n**Aturan wajib menjawab:**\n" .
                "Tidak ditemukan referensi knowledge base yang relevan dengan pesan pengguna.\n" .
                "1. Jika pesan pengguna hanya sapaan atau basa-basi ringan (halo, terima kasih, dll.), balas dengan ramah dan tawarkan bantuan seputar kesehatan ayam.\n" .
                "2. Selain itu, JANGAN menjawab dari pengetahuan umum. Minta maaf dengan ramah bahwa informasi tersebut belum tersedia, contohnya: " .
                "\"Mohon maaf, saya belum memiliki informasi tentang hal itu di basis pengetahuan saya. " .
                "Silakan tanyakan hal lain seputar kesehatan ayam, atau konsultasikan dengan dokter hewan ya 🙏\"";
        }

        // ── Conversation history ─────────────────────────────────────────
        $messages = [['role' => 'system', 'content' => $systemContent]];

        foreach ($history as $msg) {
            if ($msg->role === MessageRole::System) {
                continue;
            }
            $messages[] = [
                'role'    => $msg->role->value,
                'content' => $msg->content,
            ];
        }

        return $messages;
    }
}
