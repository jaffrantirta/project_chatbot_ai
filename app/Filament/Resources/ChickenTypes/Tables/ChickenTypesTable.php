<?php

namespace App\Filament\Resources\ChickenTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChickenTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Jenis')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record): ?string => $record->description
                        ? str($record->description)->limit(80)->value()
                        : null),

                TextColumn::make('chickens_count')
                    ->label('Jumlah Ayam')
                    ->counts('chickens')
                    ->suffix(' ekor')
                    ->sortable()
                    ->alignEnd()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0  => 'gray',
                        $state < 10   => 'info',
                        $state < 50   => 'warning',
                        default       => 'success',
                    }),

                TextColumn::make('characteristics')
                    ->label('Karakteristik')
                    ->limit(60)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->since()
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
            ->emptyStateIcon('heroicon-o-tag')
            ->emptyStateHeading('Belum ada jenis ayam')
            ->emptyStateDescription('Tambahkan jenis ayam yang ada di peternakan Anda.');
    }
}
