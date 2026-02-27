<?php

namespace App\Filament\Resources\KnowledgeChunks\Pages;

use App\Filament\Resources\KnowledgeChunks\KnowledgeChunkResource;
use App\Models\KnowledgeChunk;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListKnowledgeChunks extends ListRecords
{
    protected static string $resource = KnowledgeChunkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->icon('heroicon-o-squares-2x2')
                ->badge(KnowledgeChunk::count()),

            'belum_embed' => Tab::make('Belum Di-embed')
                ->icon('heroicon-o-clock')
                ->badgeColor('warning')
                ->badge(KnowledgeChunk::where('is_embedded', false)->count())
                ->modifyQueryUsing(fn ($query) => $query->where('is_embedded', false)),

            'sudah_embed' => Tab::make('Sudah Di-embed')
                ->icon('heroicon-o-check-circle')
                ->badgeColor('success')
                ->badge(KnowledgeChunk::where('is_embedded', true)->count())
                ->modifyQueryUsing(fn ($query) => $query->where('is_embedded', true)),
        ];
    }
}
