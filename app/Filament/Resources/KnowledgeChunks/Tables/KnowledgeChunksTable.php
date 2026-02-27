<?php

namespace App\Filament\Resources\KnowledgeChunks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class KnowledgeChunksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document.title')
                    ->label('Dokumen')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->document?->title),

                TextColumn::make('chunk_index')
                    ->label('#')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('gray')
                    ->prefix('§'),

                TextColumn::make('content')
                    ->label('Cuplikan Konten')
                    ->limit(80)
                    ->tooltip(fn ($record) => $record->content)
                    ->searchable()
                    ->wrap()
                    ->color('gray'),

                TextColumn::make('token_count')
                    ->label('Token')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state === null  => 'gray',
                        $state < 100     => 'warning',
                        $state <= 500    => 'success',
                        default          => 'danger',
                    })
                    ->suffix(' tok')
                    ->placeholder('–'),

                TextColumn::make('embedding_id')
                    ->label('Embedding ID')
                    ->copyable()
                    ->copyMessage('ID disalin!')
                    ->copyMessageDuration(1500)
                    ->icon('heroicon-o-clipboard-document')
                    ->iconPosition('after')
                    ->limit(24)
                    ->placeholder('Belum ada ID')
                    ->color('gray')
                    ->toggleable(),

                IconColumn::make('is_embedded')
                    ->label('Di-embed')
                    ->alignCenter()
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('document_id')
                    ->label('Dokumen')
                    ->relationship('document', 'title')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua dokumen'),

                TernaryFilter::make('is_embedded')
                    ->label('Status Embedding')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Di-embed')
                    ->falseLabel('Belum Di-embed'),
            ])
            ->groups([
                Group::make('document.title')
                    ->label('Dokumen')
                    ->collapsible(),
            ])
            ->defaultGroup('document.title')
            ->defaultSort('chunk_index', 'asc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
