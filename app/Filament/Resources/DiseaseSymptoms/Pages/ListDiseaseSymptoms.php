<?php

namespace App\Filament\Resources\DiseaseSymptoms\Pages;

use App\Filament\Resources\DiseaseSymptoms\DiseaseSymptomResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDiseaseSymptoms extends ListRecords
{
    protected static string $resource = DiseaseSymptomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Kata Kunci')
                ->icon('heroicon-o-plus'),
        ];
    }
}
