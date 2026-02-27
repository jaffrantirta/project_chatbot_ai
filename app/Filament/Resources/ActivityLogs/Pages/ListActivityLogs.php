<?php

namespace App\Filament\Resources\ActivityLogs\Pages;

use App\Filament\Resources\ActivityLogs\ActivityLogResource;
use App\Models\ActivityLog;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;

    // No create button — logs are system-generated
    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->icon('heroicon-o-squares-2x2')
                ->badge(ActivityLog::count()),

            'hari_ini' => Tab::make('Hari Ini')
                ->icon('heroicon-o-sun')
                ->badgeColor('info')
                ->badge(ActivityLog::whereDate('created_at', today())->count())
                ->modifyQueryUsing(fn ($query) => $query->whereDate('created_at', today())),

            'pekan_ini' => Tab::make('Pekan Ini')
                ->icon('heroicon-o-calendar-days')
                ->badgeColor('info')
                ->badge(ActivityLog::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek(),
                ])->count())
                ->modifyQueryUsing(fn ($query) => $query->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek(),
                ])),

            'aksi_data' => Tab::make('Perubahan Data')
                ->icon('heroicon-o-pencil-square')
                ->badgeColor('warning')
                ->badge(ActivityLog::where(function ($q) {
                    $q->where('action', 'like', '%create%')
                      ->orWhere('action', 'like', '%update%')
                      ->orWhere('action', 'like', '%edit%')
                      ->orWhere('action', 'like', '%delete%')
                      ->orWhere('action', 'like', '%destroy%');
                })->count())
                ->modifyQueryUsing(fn ($query) => $query->where(function ($q) {
                    $q->where('action', 'like', '%create%')
                      ->orWhere('action', 'like', '%update%')
                      ->orWhere('action', 'like', '%edit%')
                      ->orWhere('action', 'like', '%delete%')
                      ->orWhere('action', 'like', '%destroy%');
                })),

            'autentikasi' => Tab::make('Autentikasi')
                ->icon('heroicon-o-key')
                ->badgeColor('gray')
                ->badge(ActivityLog::where(function ($q) {
                    $q->where('action', 'like', '%login%')
                      ->orWhere('action', 'like', '%logout%')
                      ->orWhere('action', 'like', '%auth%')
                      ->orWhere('action', 'like', '%register%');
                })->count())
                ->modifyQueryUsing(fn ($query) => $query->where(function ($q) {
                    $q->where('action', 'like', '%login%')
                      ->orWhere('action', 'like', '%logout%')
                      ->orWhere('action', 'like', '%auth%')
                      ->orWhere('action', 'like', '%register%');
                })),
        ];
    }
}
