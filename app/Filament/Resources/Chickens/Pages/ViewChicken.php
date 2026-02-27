<?php

namespace App\Filament\Resources\Chickens\Pages;

use App\Filament\Resources\Chickens\ChickenResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;

class ViewChicken extends ViewRecord
{
    protected static string $resource = ChickenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit Data Ayam'),
            DeleteAction::make()
                ->label('Hapus'),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
