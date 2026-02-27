<?php

namespace App\Filament\Resources\Diseases\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DiseasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Penyakit')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record): ?string => $record->local_name
                        ? "Nama lokal: {$record->local_name}"
                        : null),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (?string $state): string => match (true) {
                        str_contains(strtolower((string) $state), 'virus')    => 'danger',
                        str_contains(strtolower((string) $state), 'bakteri')  => 'warning',
                        str_contains(strtolower((string) $state), 'parasit')  => 'info',
                        str_contains(strtolower((string) $state), 'mikal')    => 'success',
                        str_contains(strtolower((string) $state), 'fungi')    => 'success',
                        default                                                => 'gray',
                    }),

                TextColumn::make('disease_symptoms_count')
                    ->label('Kata Kunci')
                    ->counts('diseaseSymptoms')
                    ->suffix(' keyword')
                    ->sortable()
                    ->alignEnd()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'info' : 'gray'),

                TextColumn::make('medicines_count')
                    ->label('Obat')
                    ->counts('medicines')
                    ->suffix(' obat')
                    ->sortable()
                    ->alignEnd()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('source')
                    ->label('Sumber')
                    ->placeholder('—')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Dihapus')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('disease_category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Kategori'),

                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),

                TrashedFilter::make()
                    ->label('Data Terhapus'),
            ])
            ->defaultSort('name', 'asc')
            ->striped()
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-beaker')
            ->emptyStateHeading('Belum ada data penyakit')
            ->emptyStateDescription('Tambahkan data penyakit untuk membangun knowledge base chatbot.');
    }
}
