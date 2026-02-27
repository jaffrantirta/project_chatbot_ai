<?php

namespace App\Filament\Resources\KnowledgeBaseDocuments\Pages;

use App\Filament\Resources\KnowledgeBaseDocuments\KnowledgeBaseDocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKnowledgeBaseDocument extends ViewRecord
{
    protected static string $resource = KnowledgeBaseDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit Dokumen'),
            DeleteAction::make()
                ->label('Hapus'),
        ];
    }
}
