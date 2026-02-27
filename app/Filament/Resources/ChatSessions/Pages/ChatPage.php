<?php

namespace App\Filament\Resources\ChatSessions\Pages;

use App\Enums\ChatSessionStatus;
use App\Filament\Resources\ChatSessions\ChatSessionResource;
use App\Models\ChatSession;
use App\Services\ChatService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Livewire\Attributes\Locked;

class ChatPage extends Page
{
    protected static string $resource = ChatSessionResource::class;

    protected string $view = 'filament.resources.chat-sessions.pages.chat-page';

    protected static ?string $title = '';

    // ── State ────────────────────────────────────────────────────────────────

    // Named $session (not $record) to avoid Livewire's implicit route model binding
    // which would conflict with the {record} route parameter.
    #[Locked]
    public ?ChatSession $session = null;

    public string $userInput    = '';
    public bool   $isLoading    = false;
    public string $errorMessage = '';

    // ── Lifecycle ────────────────────────────────────────────────────────────

    public function mount(int|string $record): void
    {
        $this->session = ChatSession::with(['messages', 'user', 'farm', 'chicken'])
            ->findOrFail($record);
    }

    public function getTitle(): string
    {
        return $this->session?->title ?? 'Sesi Chat #' . $this->session?->id;
    }

    // ── Actions ──────────────────────────────────────────────────────────────

    public function sendMessage(): void
    {
        $input = trim($this->userInput);

        if ($input === '' || $this->isLoading) {
            return;
        }

        if ($this->session->status !== ChatSessionStatus::Active) {
            $this->errorMessage = 'Sesi ini sudah ditutup. Buka sesi baru untuk melanjutkan percakapan.';
            return;
        }

        $this->isLoading    = true;
        $this->userInput    = '';
        $this->errorMessage = '';

        try {
            app(ChatService::class)->sendMessage($this->session, $input);
            $this->session->refresh();
        } catch (\Throwable $e) {
            $this->errorMessage = 'Gagal menghubungi AI: ' . $e->getMessage();

            Notification::make()
                ->danger()
                ->title('Gagal mengirim pesan')
                ->body($e->getMessage())
                ->send();
        } finally {
            $this->isLoading = false;
        }

        $this->dispatch('chat-updated');
    }

    public function closeSession(): void
    {
        $this->session->update(['status' => ChatSessionStatus::Closed]);
        $this->session->refresh();

        Notification::make()
            ->success()
            ->title('Sesi ditutup')
            ->body('Percakapan telah diarsipkan.')
            ->send();
    }

    // ── Header actions ───────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('close_session')
                ->label('Tutup Sesi')
                ->icon('heroicon-o-x-circle')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Tutup sesi chat?')
                ->modalDescription('Sesi yang ditutup tidak dapat menerima pesan baru.')
                ->action(fn () => $this->closeSession())
                ->visible(fn () => $this->session?->status === ChatSessionStatus::Active),

            Action::make('view_session')
                ->label('Detail Sesi')
                ->icon('heroicon-o-information-circle')
                ->color('gray')
                ->url(fn () => ChatSessionResource::getUrl('view', ['record' => $this->session])),
        ];
    }

    // ── Computed for view ────────────────────────────────────────────────────

    public function getMessagesProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->session
            ? $this->session->messages()->orderBy('created_at')->get()
            : collect();
    }
}
