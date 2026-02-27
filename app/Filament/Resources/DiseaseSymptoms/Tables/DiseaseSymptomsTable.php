<?php

namespace App\Filament\Resources\DiseaseSymptoms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DiseaseSymptomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('disease.name')
                    ->label('Penyakit')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->description(fn ($record): ?string => $record->disease?->category?->name
                        ? "Kategori: {$record->disease->category->name}"
                        : null),

                TextColumn::make('keyword')
                    ->label('Kata Kunci')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->copyable()
                    ->copyMessage('Kata kunci disalin!')
                    ->icon('heroicon-m-magnifying-glass')
                    ->iconColor('info'),

                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('disease_id')
                    ->label('Penyakit')
                    ->relationship('disease', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Penyakit'),
            ])
            ->defaultSort('disease_id', 'asc')
            ->groups([
                \Filament\Tables\Grouping\Group::make('disease.name')
                    ->label('Penyakit')
                    ->collapsible(),
            ])
            ->defaultGroup('disease.name')
            ->striped()
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-magnifying-glass')
            ->emptyStateHeading('Belum ada kata kunci gejala')
            ->emptyStateDescription('Tambahkan kata kunci agar chatbot dapat mencocokkan gejala yang dilaporkan peternak.');
    }
}
