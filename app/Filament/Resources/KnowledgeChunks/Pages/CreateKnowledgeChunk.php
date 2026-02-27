<?php

namespace App\Filament\Resources\KnowledgeChunks\Pages;

use App\Filament\Resources\KnowledgeChunks\KnowledgeChunkResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateKnowledgeChunk extends CreateRecord
{
    protected static string $resource = KnowledgeChunkResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Chunk berhasil dibuat')
            ->body('Knowledge chunk baru telah ditambahkan ke dokumen.');
    }
}
