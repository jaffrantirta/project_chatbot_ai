<?php

namespace App\Filament\Resources\KnowledgeChunks\Tables;

use App\Jobs\EmbedKnowledgeChunkJob;
use App\Services\EmbeddingService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

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

                Action::make('embed')
                    ->label('Embed')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('success')
                    ->visible(fn ($record) => ! $record->is_embedded)
                    ->requiresConfirmation()
                    ->modalHeading('Embed Chunk Ini')
                    ->modalDescription('Chunk akan diproses melalui OpenAI Embeddings API dan hasilnya disimpan.')
                    ->action(function ($record) {
                        EmbedKnowledgeChunkJob::dispatch($record);
                        Notification::make()
                            ->success()
                            ->title('Chunk dikirim untuk embedding')
                            ->send();
                    }),

                Action::make('re_embed')
                    ->label('Re-embed')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->visible(fn ($record) => $record->is_embedded)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['is_embedded' => false, 'embedding' => null]);
                        EmbedKnowledgeChunkJob::dispatch($record);
                        Notification::make()
                            ->success()
                            ->title('Chunk dijadwalkan untuk re-embed')
                            ->send();
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('embed_selected')
                        ->label('Embed Terpilih')
                        ->icon('heroicon-o-cpu-chip')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Embed Chunks Terpilih')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $chunk) {
                                if (! $chunk->is_embedded) {
                                    EmbedKnowledgeChunkJob::dispatch($chunk);
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->success()
                                ->title("{$count} chunk dikirim untuk embedding")
                                ->send();
                        }),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->paginated([25, 50, 100]);
    }
}
