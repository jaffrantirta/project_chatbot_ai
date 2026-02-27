<?php

namespace App\Filament\Resources\Diseases\Pages;

use App\Filament\Resources\Diseases\DiseaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDiseases extends ListRecords
{
    protected static string $resource = DiseaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Penyakit')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(fn () => $this->getModel()::count()),

            'aktif' => Tab::make('Aktif')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true))
                ->badge(fn () => $this->getModel()::where('is_active', true)->count())
                ->badgeColor('success'),

            'nonaktif' => Tab::make('Nonaktif')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false))
                ->badge(fn () => $this->getModel()::where('is_active', false)->count())
                ->badgeColor('gray'),
        ];
    }
}
