<?php

namespace App\Filament\Resources\HealthRecords\Tables;

use App\Enums\HealthStatus;
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

class HealthRecordsTable
{
    private static function statusColor(HealthStatus $status): string
    {
        return match ($status) {
            HealthStatus::Sehat           => 'success',
            HealthStatus::Sakit           => 'warning',
            HealthStatus::DalamPengobatan => 'info',
            HealthStatus::Sembuh          => 'success',
            HealthStatus::Mati            => 'danger',
        };
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('record_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record): ?string => $record->follow_up_date
                        ? 'Kontrol: ' . $record->follow_up_date->format('d M Y') .
                          ($record->follow_up_date->isPast() && ! in_array($record->status, [HealthStatus::Sehat, HealthStatus::Sembuh, HealthStatus::Mati])
                              ? ' ⚠ Terlewat'
                              : '')
                        : null),

                TextColumn::make('chicken.code')
                    ->label('Ayam')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record): ?string => $record->farm?->name),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (HealthStatus $state): string => self::statusColor($state))
                    ->formatStateUsing(fn (HealthStatus $state): string => $state->label())
                    ->sortable(),

                TextColumn::make('disease.name')
                    ->label('Penyakit')
                    ->searchable()
                    ->placeholder('—')
                    ->badge()
                    ->color('danger')
                    ->limit(25),

                TextColumn::make('recordedBy.name')
                    ->label('Petugas')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                IconColumn::make('vet_consulted')
                    ->label('Dokter')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('deleted_at')
                    ->label('Dihapus')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(HealthStatus::class)
                    ->placeholder('Semua Status'),

                SelectFilter::make('farm_id')
                    ->label('Kandang')
                    ->relationship('farm', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Kandang'),

                SelectFilter::make('disease_id')
                    ->label('Penyakit')
                    ->relationship('disease', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Penyakit'),

                TernaryFilter::make('vet_consulted')
                    ->label('Konsultasi Dokter')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah')
                    ->falseLabel('Belum'),

                TrashedFilter::make()
                    ->label('Data Terhapus'),
            ])
            ->defaultSort('record_date', 'desc')
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
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->emptyStateHeading('Belum ada rekam medis')
            ->emptyStateDescription('Rekam medis dibuat otomatis dari sesi chat atau dapat diisi manual.');
    }
}
