<?php

namespace App\Filament\Resources\DiseaseCategories\Pages;

use App\Filament\Resources\DiseaseCategories\DiseaseCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDiseaseCategory extends EditRecord
{
    protected static string $resource = DiseaseCategoryResource::class;

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
        return 'Kategori penyakit berhasil diperbarui';
    }
}
