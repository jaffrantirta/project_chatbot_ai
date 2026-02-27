<?php

namespace App\Filament\Resources\DiseaseCategories;

use App\Filament\Resources\DiseaseCategories\Pages\CreateDiseaseCategory;
use App\Filament\Resources\DiseaseCategories\Pages\EditDiseaseCategory;
use App\Filament\Resources\DiseaseCategories\Pages\ListDiseaseCategories;
use App\Filament\Resources\DiseaseCategories\Pages\ViewDiseaseCategory;
use App\Filament\Resources\DiseaseCategories\Schemas\DiseaseCategoryForm;
use App\Filament\Resources\DiseaseCategories\Tables\DiseaseCategoriesTable;
use App\Models\DiseaseCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DiseaseCategoryResource extends Resource
{
    protected static ?string $model = DiseaseCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Penyakit';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Kategori Penyakit';

    protected static ?string $pluralModelLabel = 'Kategori Penyakit';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total kategori penyakit';
    }

    public static function form(Schema $schema): Schema
    {
        return DiseaseCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DiseaseCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDiseaseCategories::route('/'),
            'create' => CreateDiseaseCategory::route('/create'),
            'view'   => ViewDiseaseCategory::route('/{record}'),
            'edit'   => EditDiseaseCategory::route('/{record}/edit'),
        ];
    }
}
