<?php

namespace App\Filament\Resources\HealthRecords\Pages;

use App\Filament\Resources\HealthRecords\HealthRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;

class ViewHealthRecord extends ViewRecord
{
    protected static string $resource = HealthRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit Rekam Medis'),
            DeleteAction::make()
                ->label('Hapus'),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
