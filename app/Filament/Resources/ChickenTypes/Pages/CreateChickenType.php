<?php

namespace App\Filament\Resources\ChickenTypes\Pages;

use App\Filament\Resources\ChickenTypes\ChickenTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChickenType extends CreateRecord
{
    protected static string $resource = ChickenTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Jenis ayam berhasil ditambahkan';
    }
}
