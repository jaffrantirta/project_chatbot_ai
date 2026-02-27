<?php

namespace App\Filament\Resources\KnowledgeChunks\Pages;

use App\Filament\Resources\KnowledgeChunks\KnowledgeChunkResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditKnowledgeChunk extends EditRecord
{
    protected static string $resource = KnowledgeChunkResource::class;

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
            ->title('Chunk berhasil diperbarui')
            ->body('Perubahan pada knowledge chunk telah disimpan.');
    }
}
