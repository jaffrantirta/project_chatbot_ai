<?php

namespace App\Filament\Resources\KnowledgeBaseDocuments\Pages;

use App\Enums\DocumentType;
use App\Filament\Resources\KnowledgeBaseDocuments\KnowledgeBaseDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListKnowledgeBaseDocuments extends ListRecords
{
    protected static string $resource = KnowledgeBaseDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Dokumen')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(fn () => $this->getModel()::count()),

            'belum' => Tab::make('Belum Di-embed')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('is_processed', false))
                ->badge(fn () => $this->getModel()::where('is_processed', false)->count())
                ->badgeColor('warning'),

            'sudah' => Tab::make('Sudah Di-embed')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('is_processed', true))
                ->badge(fn () => $this->getModel()::where('is_processed', true)->count())
                ->badgeColor('success'),

            'pdf' => Tab::make('PDF')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('type', DocumentType::Pdf))
                ->badge(fn () => $this->getModel()::where('type', DocumentType::Pdf)->count())
                ->badgeColor('danger'),

            'jurnal' => Tab::make('Jurnal')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('type', DocumentType::Jurnal))
                ->badge(fn () => $this->getModel()::where('type', DocumentType::Jurnal)->count())
                ->badgeColor('success'),

            'web' => Tab::make('Web')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('type', DocumentType::Web))
                ->badge(fn () => $this->getModel()::where('type', DocumentType::Web)->count())
                ->badgeColor('warning'),

            'manual' => Tab::make('Manual')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('type', DocumentType::Manual))
                ->badge(fn () => $this->getModel()::where('type', DocumentType::Manual)->count())
                ->badgeColor('info'),
        ];
    }
}
