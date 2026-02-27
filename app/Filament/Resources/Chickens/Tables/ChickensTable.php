<?php

namespace App\Filament\Resources\Chickens\Tables;

use App\Enums\ChickenGender;
use App\Enums\ChickenStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ChickensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->copyable()
                    ->copyMessage('Kode disalin!')
                    ->description(fn ($record): string => $record->name ?? ''),

                TextColumn::make('farm.name')
                    ->label('Kandang')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-home'),

                TextColumn::make('chickenType.name')
                    ->label('Jenis')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('gender')
                    ->label('Kelamin')
                    ->badge()
                    ->color(fn (ChickenGender $state): string => match ($state) {
                        ChickenGender::Jantan => 'info',
                        ChickenGender::Betina => 'danger',
                    })
                    ->formatStateUsing(fn (ChickenGender $state): string => $state->label()),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (ChickenStatus $state): string => match ($state) {
                        ChickenStatus::Sehat   => 'success',
                        ChickenStatus::Sakit   => 'warning',
                        ChickenStatus::Mati    => 'danger',
                        ChickenStatus::Terjual => 'gray',
                    })
                    ->formatStateUsing(fn (ChickenStatus $state): string => $state->label()),

                TextColumn::make('age_weeks')
                    ->label('Usia')
                    ->suffix(' mgg')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('weight_kg')
                    ->label('Berat')
                    ->suffix(' kg')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('birth_date')
                    ->label('Tgl Lahir')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Didaftarkan')
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
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(ChickenStatus::class)
                    ->placeholder('Semua Status'),

                SelectFilter::make('gender')
                    ->label('Jenis Kelamin')
                    ->options(ChickenGender::class)
                    ->placeholder('Semua Kelamin'),

                SelectFilter::make('farm_id')
                    ->label('Kandang')
                    ->relationship('farm', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Kandang'),

                SelectFilter::make('chicken_type_id')
                    ->label('Jenis Ayam')
                    ->relationship('chickenType', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Jenis'),

                TrashedFilter::make()
                    ->label('Data Terhapus'),
            ])
            ->defaultSort('created_at', 'desc')
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
            ->emptyStateIcon('heroicon-o-heart')
            ->emptyStateHeading('Belum ada data ayam')
            ->emptyStateDescription('Mulai daftarkan ayam di peternakan Anda.');
    }
}
