<?php

namespace App\Filament\Resources\Chickens;

use App\Enums\ChickenStatus;
use App\Filament\Resources\Chickens\Pages\CreateChicken;
use App\Filament\Resources\Chickens\Pages\EditChicken;
use App\Filament\Resources\Chickens\Pages\ListChickens;
use App\Filament\Resources\Chickens\Pages\ViewChicken;
use App\Filament\Resources\Chickens\Schemas\ChickenForm;
use App\Filament\Resources\Chickens\Tables\ChickensTable;
use App\Models\Chicken;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ChickenResource extends Resource
{
    protected static ?string $model = Chicken::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Peternakan';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Ayam';

    protected static ?string $pluralModelLabel = 'Data Ayam';

    protected static ?string $recordTitleAttribute = 'code';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', ChickenStatus::Sakit)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Ayam dalam kondisi sakit';
    }

    public static function form(Schema $schema): Schema
    {
        return ChickenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChickensTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListChickens::route('/'),
            'create' => CreateChicken::route('/create'),
            'view'   => ViewChicken::route('/{record}'),
            'edit'   => EditChicken::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
