<?php

namespace App\Filament\Resources\Chickens\Pages;

use App\Filament\Resources\Chickens\ChickenResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChicken extends CreateRecord
{
    protected static string $resource = ChickenResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Ayam berhasil didaftarkan';
    }
}
