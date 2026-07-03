<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_contains(strtolower($state), 'login')    => 'info',
                        str_contains(strtolower($state), 'logout')   => 'gray',
                        str_contains(strtolower($state), 'create')   => 'success',
                        str_contains(strtolower($state), 'update')   => 'warning',
                        str_contains(strtolower($state), 'edit')     => 'warning',
                        str_contains(strtolower($state), 'delete')   => 'danger',
                        str_contains(strtolower($state), 'destroy')  => 'danger',
                        str_contains(strtolower($state), 'restore')  => 'purple',
                        default                                       => 'gray',
                    })
                    ->icon(fn (string $state): string => match (true) {
                        str_contains(strtolower($state), 'login')    => 'heroicon-o-arrow-right-on-rectangle',
                        str_contains(strtolower($state), 'logout')   => 'heroicon-o-arrow-left-on-rectangle',
                        str_contains(strtolower($state), 'create')   => 'heroicon-o-plus-circle',
                        str_contains(strtolower($state), 'update')   => 'heroicon-o-pencil-square',
                        str_contains(strtolower($state), 'edit')     => 'heroicon-o-pencil-square',
                        str_contains(strtolower($state), 'delete')   => 'heroicon-o-trash',
                        str_contains(strtolower($state), 'destroy')  => 'heroicon-o-trash',
                        str_contains(strtolower($state), 'restore')  => 'heroicon-o-arrow-uturn-left',
                        default                                       => 'heroicon-o-bolt',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->placeholder('Sistem')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user-circle')
                    ->description(fn ($record) => $record->ip_address ?? '–'),

                TextColumn::make('subject_type')
                    ->label('Subjek')
                    ->formatStateUsing(fn ($state) => $state
                        ? class_basename($state)
                        : '–'
                    )
                    ->description(fn ($record) => $record->subject_id
                        ? "ID: {$record->subject_id}"
                        : null
                    )
                    ->badge()
                    ->color('gray')
                    ->placeholder('–'),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(65)
                    ->tooltip(fn ($record) => $record->description)
                    ->placeholder('Tidak ada deskripsi')
                    ->color('gray')
                    ->wrap()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since()
                    ->sortable()
                    ->description(fn ($record) => $record->created_at->translatedFormat('d M Y, H:i')),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Pengguna')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua pengguna'),

                SelectFilter::make('action')
                    ->label('Aksi')
                    ->options(fn () => \App\Models\ActivityLog::query()
                        ->distinct()
                        ->orderBy('action')
                        ->pluck('action', 'action')
                        ->toArray()
                    )
                    ->placeholder('Semua aksi'),

                Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', today()))
                    ->toggle(),

                Filter::make('this_week')
                    ->label('Pekan Ini')
                    ->query(fn (Builder $query) => $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek(),
                    ]))
                    ->toggle(),
            ])
            ->groups([
                Group::make('action')
                    ->label('Aksi')
                    ->collapsible(),

                Group::make('user.name')
                    ->label('Pengguna')
                    ->collapsible(),

                Group::make('subject_type')
                    ->label('Tipe Subjek')
                    ->getTitleFromRecordUsing(fn ($record) => $record->subject_type
                        ? class_basename($record->subject_type)
                        : 'Tanpa Subjek'
                    )
                    ->collapsible(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->paginated([25, 50, 100]);
    }
}
