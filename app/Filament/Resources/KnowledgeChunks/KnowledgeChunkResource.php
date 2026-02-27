<?php

namespace App\Filament\Resources\KnowledgeChunks;

use App\Filament\Resources\KnowledgeChunks\Pages\CreateKnowledgeChunk;
use App\Filament\Resources\KnowledgeChunks\Pages\EditKnowledgeChunk;
use App\Filament\Resources\KnowledgeChunks\Pages\ListKnowledgeChunks;
use App\Filament\Resources\KnowledgeChunks\Pages\ViewKnowledgeChunk;
use App\Filament\Resources\KnowledgeChunks\Schemas\KnowledgeChunkForm;
use App\Filament\Resources\KnowledgeChunks\Tables\KnowledgeChunksTable;
use App\Models\KnowledgeChunk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KnowledgeChunkResource extends Resource
{
    protected static ?string $model = KnowledgeChunk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPuzzlePiece;

    protected static UnitEnum|string|null $navigationGroup = 'RAG & Chatbot';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Chunk';

    protected static ?string $pluralModelLabel = 'Knowledge Chunks';

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('is_embedded', false)->count();
        return $pending ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Chunks belum di-embed ke vector DB';
    }

    public static function form(Schema $schema): Schema
    {
        return KnowledgeChunkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KnowledgeChunksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListKnowledgeChunks::route('/'),
            'create' => CreateKnowledgeChunk::route('/create'),
            'view'   => ViewKnowledgeChunk::route('/{record}'),
            'edit'   => EditKnowledgeChunk::route('/{record}/edit'),
        ];
    }
}
