<?php

namespace App\Filament\Resources\HealthRecords\Pages;

use App\Filament\Resources\HealthRecords\HealthRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHealthRecord extends CreateRecord
{
    protected static string $resource = HealthRecordResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Rekam medis berhasil disimpan';
    }
}
