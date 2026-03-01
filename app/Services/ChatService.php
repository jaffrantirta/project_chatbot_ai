<?php

namespace App\Services;

use App\Enums\MessageRole;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\SystemPrompt;
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

        // 2. Retrieve relevant knowledge chunks via vector similarity (RAG)
        $chunks = $this->retrieveChunks($userContent);

        // 3. Build the full messages payload for OpenAI
        $history  = $session->messages()->orderBy('created_at')->get();
        $payload  = $this->buildPayload($chunks, $history);

        // 4. Call the AI
        $response = $this->client->chat()->create([
            'model'       => $session->model_used ?? config('services.openai.model', 'gpt-4o-mini'),
            'messages'    => $payload,
            'max_tokens'  => 1200,
            'temperature' => 0.7,
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
     * Vector-similarity retrieval: embed the query then rank stored chunks.
     * Falls back to an empty collection if no chunks are embedded yet.
     */
    private function retrieveChunks(string $query): \Illuminate\Database\Eloquent\Collection
    {
        return $this->embedding->findSimilarChunks($query, topK: 3);
    }

    /**
     * Build the messages array to send to OpenAI.
     * Injects active system prompt + retrieved context at the top.
     */
    private function buildPayload(
        \Illuminate\Database\Eloquent\Collection $chunks,
        \Illuminate\Database\Eloquent\Collection $history
    ): array {
        $systemContent = SystemPrompt::where('is_active', true)
            ->value('content')
            ?? 'Kamu adalah asisten AI untuk monitoring kesehatan ayam. Jawab dengan ramah dan berbasis fakta.';

        if ($chunks->isNotEmpty()) {
            $systemContent .= "\n\n---\n**Konteks dari knowledge base:**\n\n";
            foreach ($chunks as $chunk) {
                $systemContent .= $chunk->content . "\n\n";
            }
        }

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
