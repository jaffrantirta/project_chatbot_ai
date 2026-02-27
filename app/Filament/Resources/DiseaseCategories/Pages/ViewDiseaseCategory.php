<?php

namespace App\Filament\Resources\DiseaseCategories\Pages;

use App\Filament\Resources\DiseaseCategories\DiseaseCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDiseaseCategory extends ViewRecord
{
    protected static string $resource = DiseaseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit Kategori'),
            DeleteAction::make()
                ->label('Hapus'),
        ];
    }
}
