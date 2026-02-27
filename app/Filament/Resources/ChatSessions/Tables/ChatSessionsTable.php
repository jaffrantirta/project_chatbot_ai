<?php

namespace App\Filament\Resources\ChatSessions\Tables;

use App\Enums\ChatSessionStatus;
use App\Filament\Resources\ChatSessions\ChatSessionResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class ChatSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Sesi')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->placeholder('Tanpa judul')
                    ->description(fn($record) => $record->session_token
                        ? \Illuminate\Support\Str::limit($record->session_token, 28)
                        : null),
                TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->sortable()
                    ->icon('heroicon-o-user-circle')
                    ->searchable(),
                TextColumn::make('farm.name')
                    ->label('Kandang')
                    ->placeholder('–')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(ChatSessionStatus $state): string => match ($state) {
                        ChatSessionStatus::Active => 'success',
                        ChatSessionStatus::Closed => 'gray',
                    })
                    ->formatStateUsing(fn(ChatSessionStatus $state) => $state->label())
                    ->icon(fn(ChatSessionStatus $state): string => match ($state) {
                        ChatSessionStatus::Active => 'heroicon-o-signal',
                        ChatSessionStatus::Closed => 'heroicon-o-x-circle',
                    }),
                TextColumn::make('model_used')
                    ->label('Model')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('total_tokens_used')
                    ->label('Token')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn($state): string => match (true) {
                        $state < 1000 => 'success',
                        $state < 5000 => 'warning',
                        default => 'danger',
                    })
                    ->suffix(' tok')
                    ->toggleable(),
                TextColumn::make('messages_count')
                    ->label('Pesan')
                    ->counts('messages')
                    ->alignCenter()
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Terakhir Aktif')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(ChatSessionStatus::class)
                    ->placeholder('Semua status'),
                SelectFilter::make('user_id')
                    ->label('Pengguna')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua pengguna'),
                SelectFilter::make('farm_id')
                    ->label('Kandang')
                    ->relationship('farm', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua kandang'),
            ])
            ->groups([
                Group::make('user.name')
                    ->label('Pengguna')
                    ->collapsible(),
                Group::make('status')
                    ->label('Status')
                    ->getTitleFromRecordUsing(fn($record) => $record->status->label())
                    ->collapsible(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                Action::make('chat')
                    ->label('Mulai Chat')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn($record) => ChatSessionResource::getUrl('chat', ['record' => $record]))
                    ->visible(fn($record) => $record->status === ChatSessionStatus::Active),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->paginated([15, 25, 50]);
    }
}
