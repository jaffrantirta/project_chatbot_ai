<?php

namespace App\Filament\Resources\ChickenTypes\Pages;

use App\Filament\Resources\ChickenTypes\ChickenTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChickenTypes extends ListRecords
{
    protected static string $resource = ChickenTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Jenis Ayam')
                ->icon('heroicon-o-plus'),
        ];
    }
}
