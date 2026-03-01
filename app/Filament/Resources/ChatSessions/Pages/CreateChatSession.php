<?php

namespace App\Filament\Resources\ChatSessions\Pages;

use App\Filament\Resources\ChatSessions\ChatSessionResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateChatSession extends CreateRecord
{
    protected static string $resource = ChatSessionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['session_token'])) {
            $data['session_token'] = Str::uuid()->toString();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        // Open the public chat page directly after creating a session
        return route('chat.show', $this->record->session_token);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Sesi chat berhasil dibuat')
            ->body('Membuka halaman chat…');
    }
}
