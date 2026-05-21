<?php

namespace App\Filament\Resources\ChickenTypes;

use App\Filament\Resources\ChickenTypes\Pages\CreateChickenType;
use App\Filament\Resources\ChickenTypes\Pages\EditChickenType;
use App\Filament\Resources\ChickenTypes\Pages\ListChickenTypes;
use App\Filament\Resources\ChickenTypes\Pages\ViewChickenType;
use App\Filament\Resources\ChickenTypes\Schemas\ChickenTypeForm;
use App\Filament\Resources\ChickenTypes\Tables\ChickenTypesTable;
use App\Models\ChickenType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ChickenTypeResource extends Resource
{
    protected static ?string $model = ChickenType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Peternakan';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Jenis Ayam';

    protected static ?string $pluralModelLabel = 'Data Jenis Ayam';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total jenis ayam';
    }

    public static function form(Schema $schema): Schema
    {
        return ChickenTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChickenTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListChickenTypes::route('/'),
            'create' => CreateChickenType::route('/create'),
            'view'   => ViewChickenType::route('/{record}'),
            'edit'   => EditChickenType::route('/{record}/edit'),
        ];
    }
}
