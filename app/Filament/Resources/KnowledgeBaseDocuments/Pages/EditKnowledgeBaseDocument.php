<?php

namespace App\Filament\Resources\KnowledgeBaseDocuments\Pages;

use App\Filament\Resources\KnowledgeBaseDocuments\KnowledgeBaseDocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditKnowledgeBaseDocument extends EditRecord
{
    protected static string $resource = KnowledgeBaseDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Dokumen berhasil diperbarui';
    }
}
