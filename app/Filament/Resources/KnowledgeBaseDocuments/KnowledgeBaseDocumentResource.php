<?php

namespace App\Filament\Resources\KnowledgeBaseDocuments;

use App\Filament\Resources\KnowledgeBaseDocuments\Pages\CreateKnowledgeBaseDocument;
use App\Filament\Resources\KnowledgeBaseDocuments\Pages\EditKnowledgeBaseDocument;
use App\Filament\Resources\KnowledgeBaseDocuments\Pages\ListKnowledgeBaseDocuments;
use App\Filament\Resources\KnowledgeBaseDocuments\Pages\ViewKnowledgeBaseDocument;
use App\Filament\Resources\KnowledgeBaseDocuments\Schemas\KnowledgeBaseDocumentForm;
use App\Filament\Resources\KnowledgeBaseDocuments\Tables\KnowledgeBaseDocumentsTable;
use App\Models\KnowledgeBaseDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KnowledgeBaseDocumentResource extends Resource
{
    protected static ?string $model = KnowledgeBaseDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static UnitEnum|string|null $navigationGroup = 'RAG & Chatbot';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Dokumen';

    protected static ?string $pluralModelLabel = 'Knowledge Base';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('is_processed', false)->count();
        return $pending ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Dokumen belum diproses / di-embed';
    }

    public static function form(Schema $schema): Schema
    {
        return KnowledgeBaseDocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KnowledgeBaseDocumentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListKnowledgeBaseDocuments::route('/'),
            'create' => CreateKnowledgeBaseDocument::route('/create'),
            'view'   => ViewKnowledgeBaseDocument::route('/{record}'),
            'edit'   => EditKnowledgeBaseDocument::route('/{record}/edit'),
        ];
    }
}
