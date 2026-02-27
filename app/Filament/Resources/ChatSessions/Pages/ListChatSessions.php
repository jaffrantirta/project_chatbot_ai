<?php

namespace App\Filament\Resources\ChatSessions\Pages;

use App\Enums\ChatSessionStatus;
use App\Filament\Resources\ChatSessions\ChatSessionResource;
use App\Models\ChatSession;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListChatSessions extends ListRecords
{
    protected static string $resource = ChatSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Sesi Baru'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->icon('heroicon-o-squares-2x2')
                ->badge(ChatSession::count()),

            'aktif' => Tab::make('Aktif')
                ->icon('heroicon-o-signal')
                ->badgeColor('success')
                ->badge(ChatSession::where('status', ChatSessionStatus::Active)->count())
                ->modifyQueryUsing(fn ($q) => $q->where('status', ChatSessionStatus::Active)),

            'ditutup' => Tab::make('Ditutup')
                ->icon('heroicon-o-x-circle')
                ->badgeColor('gray')
                ->badge(ChatSession::where('status', ChatSessionStatus::Closed)->count())
                ->modifyQueryUsing(fn ($q) => $q->where('status', ChatSessionStatus::Closed)),
        ];
    }
}
