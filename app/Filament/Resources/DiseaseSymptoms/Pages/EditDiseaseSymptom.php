<?php

namespace App\Filament\Resources\DiseaseSymptoms\Pages;

use App\Filament\Resources\DiseaseSymptoms\DiseaseSymptomResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDiseaseSymptom extends EditRecord
{
    protected static string $resource = DiseaseSymptomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Kata Kunci'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Kata kunci berhasil diperbarui';
    }
}
