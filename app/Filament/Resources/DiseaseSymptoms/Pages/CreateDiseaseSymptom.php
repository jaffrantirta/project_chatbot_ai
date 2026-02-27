<?php

namespace App\Filament\Resources\DiseaseSymptoms\Pages;

use App\Filament\Resources\DiseaseSymptoms\DiseaseSymptomResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDiseaseSymptom extends CreateRecord
{
    protected static string $resource = DiseaseSymptomResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Kata kunci berhasil ditambahkan';
    }
}
