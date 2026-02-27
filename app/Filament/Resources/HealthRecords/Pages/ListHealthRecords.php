<?php

namespace App\Filament\Resources\HealthRecords\Pages;

use App\Enums\HealthStatus;
use App\Filament\Resources\HealthRecords\HealthRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListHealthRecords extends ListRecords
{
    protected static string $resource = HealthRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Rekam Medis')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(fn () => $this->getModel()::count()),

            'sakit' => Tab::make('Sakit')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', HealthStatus::Sakit))
                ->badge(fn () => $this->getModel()::where('status', HealthStatus::Sakit)->count())
                ->badgeColor('warning'),

            'dalam_pengobatan' => Tab::make('Dalam Pengobatan')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', HealthStatus::DalamPengobatan))
                ->badge(fn () => $this->getModel()::where('status', HealthStatus::DalamPengobatan)->count())
                ->badgeColor('info'),

            'sembuh' => Tab::make('Sembuh')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', HealthStatus::Sembuh))
                ->badge(fn () => $this->getModel()::where('status', HealthStatus::Sembuh)->count())
                ->badgeColor('success'),

            'sehat' => Tab::make('Sehat')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', HealthStatus::Sehat))
                ->badge(fn () => $this->getModel()::where('status', HealthStatus::Sehat)->count())
                ->badgeColor('success'),

            'mati' => Tab::make('Mati')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', HealthStatus::Mati))
                ->badge(fn () => $this->getModel()::where('status', HealthStatus::Mati)->count())
                ->badgeColor('danger'),
        ];
    }
}
