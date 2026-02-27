<?php

namespace App\Filament\Resources\DiseaseCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DiseaseCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record): ?string => $record->description
                        ? str($record->description)->limit(90)->value()
                        : null),

                TextColumn::make('diseases_count')
                    ->label('Jumlah Penyakit')
                    ->counts('diseases')
                    ->suffix(' penyakit')
                    ->sortable()
                    ->alignEnd()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'gray',
                        $state < 5   => 'info',
                        $state < 15  => 'warning',
                        default      => 'danger',
                    }),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                ]),
            ])
            ->emptyStateIcon('heroicon-o-folder-open')
            ->emptyStateHeading('Belum ada kategori penyakit')
            ->emptyStateDescription('Tambahkan kategori seperti Virus, Bakteri, Parasit, atau Mikal/Fungi.');
    }
}
