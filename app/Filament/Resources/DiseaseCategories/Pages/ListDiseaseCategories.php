<?php

namespace App\Filament\Resources\DiseaseCategories\Pages;

use App\Filament\Resources\DiseaseCategories\DiseaseCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDiseaseCategories extends ListRecords
{
    protected static string $resource = DiseaseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Kategori')
                ->icon('heroicon-o-plus'),
        ];
    }
}
