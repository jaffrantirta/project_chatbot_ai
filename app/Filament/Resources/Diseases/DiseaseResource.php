<?php

namespace App\Filament\Resources\Diseases;

use App\Filament\Resources\Diseases\Pages\CreateDisease;
use App\Filament\Resources\Diseases\Pages\EditDisease;
use App\Filament\Resources\Diseases\Pages\ListDiseases;
use App\Filament\Resources\Diseases\Pages\ViewDisease;
use App\Filament\Resources\Diseases\Schemas\DiseaseForm;
use App\Filament\Resources\Diseases\Tables\DiseasesTable;
use App\Models\Disease;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class DiseaseResource extends Resource
{
    protected static ?string $model = Disease::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Penyakit';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Penyakit';

    protected static ?string $pluralModelLabel = 'Data Penyakit';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total penyakit aktif dalam knowledge base';
    }

    public static function form(Schema $schema): Schema
    {
        return DiseaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DiseasesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDiseases::route('/'),
            'create' => CreateDisease::route('/create'),
            'view'   => ViewDisease::route('/{record}'),
            'edit'   => EditDisease::route('/{record}/edit'),
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
