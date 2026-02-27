<?php

namespace App\Filament\Resources\KnowledgeBaseDocuments\Pages;

use App\Filament\Resources\KnowledgeBaseDocuments\KnowledgeBaseDocumentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKnowledgeBaseDocument extends CreateRecord
{
    protected static string $resource = KnowledgeBaseDocumentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Dokumen berhasil ditambahkan ke knowledge base';
    }
}
