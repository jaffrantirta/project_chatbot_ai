<?php

namespace App\Filament\Resources\SystemPrompts\Pages;

use App\Filament\Resources\SystemPrompts\SystemPromptResource;
use App\Models\SystemPrompt;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListSystemPrompts extends ListRecords
{
    protected static string $resource = SystemPromptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $active   = SystemPrompt::where('is_active', true)->count();
        $inactive = SystemPrompt::where('is_active', false)->count();

        return [
            'semua' => Tab::make('Semua')
                ->icon('heroicon-o-squares-2x2')
                ->badge(SystemPrompt::count()),

            'aktif' => Tab::make('Aktif')
                ->icon('heroicon-o-bolt')
                ->badgeColor($active === 1 ? 'success' : ($active === 0 ? 'danger' : 'warning'))
                ->badge($active)
                ->modifyQueryUsing(fn ($query) => $query->where('is_active', true)),

            'nonaktif' => Tab::make('Nonaktif')
                ->icon('heroicon-o-bolt-slash')
                ->badgeColor('gray')
                ->badge($inactive)
                ->modifyQueryUsing(fn ($query) => $query->where('is_active', false)),
        ];
    }
}
