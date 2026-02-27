<?php

namespace App\Filament\Resources\DiseaseCategories\Pages;

use App\Filament\Resources\DiseaseCategories\DiseaseCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDiseaseCategory extends CreateRecord
{
    protected static string $resource = DiseaseCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Kategori penyakit berhasil ditambahkan';
    }
}
