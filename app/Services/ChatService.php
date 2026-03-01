<?php

namespace App\Services;

use App\Enums\MessageRole;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\KnowledgeChunk;
use App\Models\SystemPrompt;
use Illuminate\Support\Str;
use OpenAI\Client;

class ChatService
{
    private Client $client;

    public function __construct()
    {
        $this->client = \OpenAI::factory()
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

        // 2. Retrieve relevant knowledge chunks (keyword-based RAG)
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
     * Simple keyword-based retrieval until a vector DB is in place.
     * Returns up to 3 embedded chunks whose content matches any keyword.
     */
    private function retrieveChunks(string $query): \Illuminate\Database\Eloquent\Collection
    {
        $keywords = collect(explode(' ', strtolower($query)))
            ->map(fn ($w) => preg_replace('/[^a-z0-9]/', '', $w))
            ->filter(fn ($w) => strlen($w) > 3)
            ->unique()
            ->take(6);

        if ($keywords->isEmpty()) {
            return KnowledgeChunk::whereNull('id')->get();
        }

        $q = KnowledgeChunk::where('is_embedded', true);
        foreach ($keywords as $keyword) {
            $q->orWhere('content', 'like', "%{$keyword}%");
        }

        return $q->limit(3)->get();
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
