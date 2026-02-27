<?php

namespace App\Filament\Resources\SystemPrompts\Pages;

use App\Filament\Resources\SystemPrompts\SystemPromptResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSystemPrompt extends CreateRecord
{
    protected static string $resource = SystemPromptResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('System prompt berhasil dibuat')
            ->body('Prompt baru telah ditambahkan. Aktifkan jika sudah siap digunakan.');
    }
}
