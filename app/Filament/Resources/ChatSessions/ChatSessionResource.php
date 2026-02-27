<?php

namespace App\Filament\Resources\ChatSessions;

use App\Enums\ChatSessionStatus;
use App\Filament\Resources\ChatSessions\Pages\ChatPage;
use App\Filament\Resources\ChatSessions\Pages\CreateChatSession;
use App\Filament\Resources\ChatSessions\Pages\EditChatSession;
use App\Filament\Resources\ChatSessions\Pages\ListChatSessions;
use App\Filament\Resources\ChatSessions\Pages\ViewChatSession;
use App\Filament\Resources\ChatSessions\Schemas\ChatSessionForm;
use App\Filament\Resources\ChatSessions\Tables\ChatSessionsTable;
use App\Models\ChatSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ChatSessionResource extends Resource
{
    protected static ?string $model = ChatSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static UnitEnum|string|null $navigationGroup = 'RAG & Chatbot';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Sesi Chat';

    protected static ?string $pluralModelLabel = 'Chat Sessions';

    public static function getNavigationBadge(): ?string
    {
        $active = static::getModel()::where('status', ChatSessionStatus::Active)->count();
        return $active ? (string) $active : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Sesi chat yang sedang aktif';
    }

    public static function form(Schema $schema): Schema
    {
        return ChatSessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChatSessionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListChatSessions::route('/'),
            'create' => CreateChatSession::route('/create'),
            'view'   => ViewChatSession::route('/{record}'),
            'edit'   => EditChatSession::route('/{record}/edit'),
            'chat'   => ChatPage::route('/{record}/chat'),
        ];
    }
}
