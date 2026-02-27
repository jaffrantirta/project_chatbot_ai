<?php

namespace App\Filament\Resources\DiseaseSymptoms;

use App\Filament\Resources\DiseaseSymptoms\Pages\CreateDiseaseSymptom;
use App\Filament\Resources\DiseaseSymptoms\Pages\EditDiseaseSymptom;
use App\Filament\Resources\DiseaseSymptoms\Pages\ListDiseaseSymptoms;
use App\Filament\Resources\DiseaseSymptoms\Schemas\DiseaseSymptomForm;
use App\Filament\Resources\DiseaseSymptoms\Tables\DiseaseSymptomsTable;
use App\Models\DiseaseSymptom;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DiseaseSymptomResource extends Resource
{
    protected static ?string $model = DiseaseSymptom::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Penyakit';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Kata Kunci Gejala';

    protected static ?string $pluralModelLabel = 'Kata Kunci Gejala';

    protected static ?string $recordTitleAttribute = 'keyword';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total kata kunci untuk similarity search';
    }

    public static function form(Schema $schema): Schema
    {
        return DiseaseSymptomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DiseaseSymptomsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDiseaseSymptoms::route('/'),
            'create' => CreateDiseaseSymptom::route('/create'),
            'edit'   => EditDiseaseSymptom::route('/{record}/edit'),
        ];
    }
}
