<?php

namespace App\Filament\Resources\Diseases\Pages;

use App\Filament\Resources\Diseases\DiseaseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDisease extends CreateRecord
{
    protected static string $resource = DiseaseResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data penyakit berhasil ditambahkan ke knowledge base';
    }
}
