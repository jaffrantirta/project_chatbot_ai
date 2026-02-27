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
        // Auto-generate a session token if not provided
        if (empty($data['session_token'])) {
            $data['session_token'] = Str::uuid()->toString();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('chat', ['record' => $this->record]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Sesi chat berhasil dibuat')
            ->body('Memuat antarmuka percakapan…');
    }
}
