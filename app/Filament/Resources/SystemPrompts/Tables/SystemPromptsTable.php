<?php

namespace App\Filament\Resources\SystemPrompts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SystemPromptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_active')
                    ->label('')
                    ->alignCenter()
                    ->boolean()
                    ->trueIcon('heroicon-o-bolt')
                    ->falseIcon('heroicon-o-bolt-slash')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->width('40px'),

                TextColumn::make('name')
                    ->label('Nama Prompt')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record): string => $record->notes
                        ? \Illuminate\Support\Str::limit(strip_tags($record->notes), 60)
                        : 'Tidak ada catatan'
                    ),

                TextColumn::make('version')
                    ->label('Versi')
                    ->badge()
                    ->color('info')
                    ->placeholder('–')
                    ->sortable(),

                TextColumn::make('content')
                    ->label('Cuplikan Prompt')
                    ->limit(70)
                    ->tooltip(fn ($record) => \Illuminate\Support\Str::limit($record->content, 300))
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->sortable()
                    ->icon('heroicon-o-user-circle')
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),

                SelectFilter::make('created_by')
                    ->label('Dibuat Oleh')
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua pengguna'),
            ])
            ->defaultSort('is_active', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->striped();
    }
}
