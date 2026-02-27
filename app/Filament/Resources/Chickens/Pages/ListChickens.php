<?php

namespace App\Filament\Resources\Chickens\Pages;

use App\Enums\ChickenStatus;
use App\Filament\Resources\Chickens\ChickenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListChickens extends ListRecords
{
    protected static string $resource = ChickenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Daftarkan Ayam')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(fn () => $this->getModel()::count()),

            'sehat' => Tab::make('Sehat')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ChickenStatus::Sehat))
                ->badge(fn () => $this->getModel()::where('status', ChickenStatus::Sehat)->count())
                ->badgeColor('success'),

            'sakit' => Tab::make('Sakit')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ChickenStatus::Sakit))
                ->badge(fn () => $this->getModel()::where('status', ChickenStatus::Sakit)->count())
                ->badgeColor('warning'),

            'mati' => Tab::make('Mati')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ChickenStatus::Mati))
                ->badge(fn () => $this->getModel()::where('status', ChickenStatus::Mati)->count())
                ->badgeColor('danger'),

            'terjual' => Tab::make('Terjual')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ChickenStatus::Terjual))
                ->badge(fn () => $this->getModel()::where('status', ChickenStatus::Terjual)->count())
                ->badgeColor('gray'),
        ];
    }
}
