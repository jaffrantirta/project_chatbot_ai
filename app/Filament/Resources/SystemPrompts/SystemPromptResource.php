<?php

namespace App\Filament\Resources\SystemPrompts;

use App\Filament\Resources\SystemPrompts\Pages\CreateSystemPrompt;
use App\Filament\Resources\SystemPrompts\Pages\EditSystemPrompt;
use App\Filament\Resources\SystemPrompts\Pages\ListSystemPrompts;
use App\Filament\Resources\SystemPrompts\Pages\ViewSystemPrompt;
use App\Filament\Resources\SystemPrompts\Schemas\SystemPromptForm;
use App\Filament\Resources\SystemPrompts\Tables\SystemPromptsTable;
use App\Models\SystemPrompt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SystemPromptResource extends Resource
{
    protected static ?string $model = SystemPrompt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCommandLine;

    protected static UnitEnum|string|null $navigationGroup = 'RAG & Chatbot';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'System Prompt';

    protected static ?string $pluralModelLabel = 'System Prompts';

    public static function getNavigationBadge(): ?string
    {
        $active = static::getModel()::where('is_active', true)->count();
        return $active ? (string) $active : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $active = static::getModel()::where('is_active', true)->count();
        // Ideal = exactly 1 active prompt; 0 = danger, >1 = warning, 1 = success
        return match (true) {
            $active === 0  => 'danger',
            $active === 1  => 'success',
            default        => 'warning',
        };
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $active = static::getModel()::where('is_active', true)->count();

        return match (true) {
            $active === 0  => 'Tidak ada prompt aktif!',
            $active === 1  => '1 prompt aktif',
            default        => "{$active} prompt aktif — seharusnya hanya 1",
        };
    }

    public static function form(Schema $schema): Schema
    {
        return SystemPromptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SystemPromptsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListSystemPrompts::route('/'),
            'create' => CreateSystemPrompt::route('/create'),
            'view'   => ViewSystemPrompt::route('/{record}'),
            'edit'   => EditSystemPrompt::route('/{record}/edit'),
        ];
    }
}
