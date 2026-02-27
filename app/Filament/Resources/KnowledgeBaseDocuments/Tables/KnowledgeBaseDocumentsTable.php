<?php

namespace App\Filament\Resources\KnowledgeBaseDocuments\Tables;

use App\Enums\DocumentType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class KnowledgeBaseDocumentsTable
{
    private static function typeIcon(DocumentType $type): string
    {
        return match ($type) {
            DocumentType::Pdf    => 'heroicon-o-document',
            DocumentType::Manual => 'heroicon-o-book-open',
            DocumentType::Jurnal => 'heroicon-o-academic-cap',
            DocumentType::Web    => 'heroicon-o-globe-alt',
        };
    }

    private static function typeColor(DocumentType $type): string
    {
        return match ($type) {
            DocumentType::Pdf    => 'danger',
            DocumentType::Manual => 'info',
            DocumentType::Jurnal => 'success',
            DocumentType::Web    => 'warning',
        };
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Dokumen')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->wrap()
                    ->description(fn ($record): ?string => $record->source_url
                        ? str($record->source_url)->limit(60)->value()
                        : ($record->file_path ? "📁 {$record->file_path}" : null)),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->icon(fn (DocumentType $state): string => self::typeIcon($state))
                    ->color(fn (DocumentType $state): string => self::typeColor($state))
                    ->formatStateUsing(fn (DocumentType $state): string => $state->label())
                    ->sortable(),

                TextColumn::make('chunks_count')
                    ->label('Chunks')
                    ->counts('chunks')
                    ->suffix(' chunk')
                    ->sortable()
                    ->alignEnd()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'gray',
                        $state < 10  => 'warning',
                        default      => 'success',
                    }),

                IconColumn::make('is_processed')
                    ->label('Di-embed')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

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
                    ->label('Tipe Dokumen')
                    ->options(DocumentType::class)
                    ->placeholder('Semua Tipe'),

                TernaryFilter::make('is_processed')
                    ->label('Status Embedding')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Di-embed')
                    ->falseLabel('Belum Di-embed'),
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
                ]),
            ])
            ->emptyStateIcon('heroicon-o-book-open')
            ->emptyStateHeading('Belum ada dokumen')
            ->emptyStateDescription('Tambahkan dokumen PDF, jurnal, atau manual sebagai sumber knowledge base chatbot.');
    }
}
