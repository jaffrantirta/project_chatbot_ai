<?php

namespace App\Filament\Resources\SystemPrompts\Pages;

use App\Filament\Resources\SystemPrompts\SystemPromptResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSystemPrompt extends EditRecord
{
    protected static string $resource = SystemPromptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('System prompt berhasil diperbarui')
            ->body('Perubahan telah disimpan. Pastikan hanya satu prompt yang aktif.');
    }
}
