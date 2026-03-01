<?php

namespace App\Http\Controllers;

use App\Enums\ChatSessionStatus;
use App\Models\ChatSession;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    /**
     * Show the public chat page for a session identified by its token.
     */
    public function show(string $token): Response
    {
        $session = ChatSession::where('session_token', $token)
            ->with(['messages', 'farm', 'chicken'])
            ->firstOrFail();

        $messages = $session->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn ($m) => [
                'id'         => $m->id,
                'role'       => $m->role->value,
                'content'    => $m->content,
                'tokens'     => $m->tokens_used,
                'created_at' => $m->created_at->translatedFormat('H:i'),
            ]);

        return Inertia::render('Chat', [
            'session' => [
                'id'           => $session->id,
                'token'        => $session->session_token,
                'title'        => $session->title ?? 'Asisten Kesehatan Ayam',
                'model'        => $session->model_used,
                'status'       => $session->status->value,
                'tokens_used'  => $session->total_tokens_used,
                'farm'         => $session->farm?->name,
                'chicken'      => $session->chicken?->code,
            ],
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message and stream back the AI response.
     */
    public function send(Request $request, string $token)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $session = ChatSession::where('session_token', $token)
            ->with(['farm', 'chicken'])
            ->firstOrFail();

        if ($session->status !== ChatSessionStatus::Active) {
            return response()->json([
                'error' => 'Sesi ini sudah ditutup.',
            ], 403);
        }

        $assistantMessage = app(ChatService::class)->sendMessage(
            $session,
            $request->input('message')
        );

        return response()->json([
            'message' => [
                'id'         => $assistantMessage->id,
                'role'       => $assistantMessage->role->value,
                'content'    => $assistantMessage->content,
                'tokens'     => $assistantMessage->tokens_used,
                'created_at' => $assistantMessage->created_at->translatedFormat('H:i'),
            ],
            'tokens_used' => $session->fresh()->total_tokens_used,
        ]);
    }
}
