<?php

namespace App\Filament\Resources\Medicines\Tables;

use App\Enums\MedicineType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MedicinesTable
{
    // Badge colour per medicine type
    private static function typeColor(MedicineType $type): string
    {
        return match ($type) {
            MedicineType::Antibiotik  => 'danger',
            MedicineType::Vaksin      => 'info',
            MedicineType::Vitamin     => 'success',
            MedicineType::Antiparasit => 'warning',
            MedicineType::Antifungi   => 'warning',
            MedicineType::Lainnya     => 'gray',
        };
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Obat / Vaksin')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record): ?string => $record->description
                        ? str($record->description)->limit(70)->value()
                        : null),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (MedicineType $state): string => self::typeColor($state))
                    ->formatStateUsing(fn (MedicineType $state): string => $state->label())
                    ->sortable(),

                TextColumn::make('diseases_count')
                    ->label('Penyakit')
                    ->counts('diseases')
                    ->suffix(' penyakit')
                    ->sortable()
                    ->alignEnd()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'warning' : 'gray'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe Produk')
                    ->options(MedicineType::class)
                    ->placeholder('Semua Tipe'),

                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),
            ])
            ->groups([
                \Filament\Tables\Grouping\Group::make('type')
                    ->label('Tipe Produk')
                    ->collapsible(),
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
            ->emptyStateIcon('heroicon-o-beaker')
            ->emptyStateHeading('Belum ada data obat')
            ->emptyStateDescription('Tambahkan obat, vaksin, dan suplemen yang digunakan di peternakan.');
    }
}
