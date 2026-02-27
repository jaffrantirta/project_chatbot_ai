<?php

namespace App\Filament\Resources\Medicines\Pages;

use App\Enums\MedicineType;
use App\Filament\Resources\Medicines\MedicineResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListMedicines extends ListRecords
{
    protected static string $resource = MedicineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Obat / Vaksin')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(fn () => $this->getModel()::count()),

            'antibiotik' => Tab::make('Antibiotik')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('type', MedicineType::Antibiotik))
                ->badge(fn () => $this->getModel()::where('type', MedicineType::Antibiotik)->count())
                ->badgeColor('danger'),

            'vaksin' => Tab::make('Vaksin')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('type', MedicineType::Vaksin))
                ->badge(fn () => $this->getModel()::where('type', MedicineType::Vaksin)->count())
                ->badgeColor('info'),

            'vitamin' => Tab::make('Vitamin')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('type', MedicineType::Vitamin))
                ->badge(fn () => $this->getModel()::where('type', MedicineType::Vitamin)->count())
                ->badgeColor('success'),

            'antiparasit' => Tab::make('Antiparasit')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('type', MedicineType::Antiparasit))
                ->badge(fn () => $this->getModel()::where('type', MedicineType::Antiparasit)->count())
                ->badgeColor('warning'),

            'antifungi' => Tab::make('Antifungi')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('type', MedicineType::Antifungi))
                ->badge(fn () => $this->getModel()::where('type', MedicineType::Antifungi)->count())
                ->badgeColor('warning'),

            'lainnya' => Tab::make('Lainnya')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('type', MedicineType::Lainnya))
                ->badge(fn () => $this->getModel()::where('type', MedicineType::Lainnya)->count())
                ->badgeColor('gray'),
        ];
    }
}
