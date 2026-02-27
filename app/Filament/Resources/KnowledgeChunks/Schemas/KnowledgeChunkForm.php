<?php

namespace App\Filament\Resources\KnowledgeChunks\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KnowledgeChunkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->columnSpanFull()
                    ->schema([

                        // ── Main content (2/3) ────────────────────────────
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 2])
                            ->schema([

                                Section::make('Identitas Chunk')
                                    ->description('Dokumen induk dan posisi urutan chunk')
                                    ->icon('heroicon-o-puzzle-piece')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('document_id')
                                            ->label('Dokumen Induk')
                                            ->relationship('document', 'title')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->placeholder('Pilih dokumen…')
                                            ->columnSpanFull(),

                                        TextInput::make('chunk_index')
                                            ->label('Indeks Chunk')
                                            ->numeric()
                                            ->required()
                                            ->minValue(0)
                                            ->helperText('Urutan potongan dalam dokumen, dimulai dari 0.'),

                                        TextInput::make('token_count')
                                            ->label('Jumlah Token')
                                            ->numeric()
                                            ->minValue(0)
                                            ->suffix('tokens')
                                            ->helperText('Diisi otomatis saat proses chunking.'),
                                    ]),

                                Section::make('Isi Chunk')
                                    ->description('Potongan teks yang akan di-embed ke vector database')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Konten')
                                            ->placeholder('Potongan teks dari dokumen induk…')
                                            ->rows(10)
                                            ->required()
                                            ->helperText('Idealnya 200–500 token per chunk untuk hasil retrieval terbaik.')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Metadata')
                                    ->description('Informasi kontekstual tambahan untuk chunk ini')
                                    ->icon('heroicon-o-code-bracket')
                                    ->collapsed()
                                    ->schema([
                                        KeyValue::make('metadata')
                                            ->label('')
                                            ->keyLabel('Key')
                                            ->valueLabel('Value')
                                            ->addActionLabel('+ Tambah Metadata')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ── Sidebar (1/3) ─────────────────────────────────
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 1])
                            ->schema([

                                Section::make('Status Embedding')
                                    ->description('Status dan ID vektor di vector database')
                                    ->icon('heroicon-o-cpu-chip')
                                    ->schema([
                                        Toggle::make('is_embedded')
                                            ->label('Sudah Di-embed')
                                            ->helperText('Aktifkan setelah chunk berhasil diproses ke vector DB.')
                                            ->onColor('success')
                                            ->offColor('warning'),

                                        TextInput::make('embedding_id')
                                            ->label('Embedding ID')
                                            ->placeholder('e.g. chunk-doc1-0001')
                                            ->helperText('ID unik di vector DB (Pinecone, ChromaDB, dll.).')
                                            ->maxLength(255),
                                    ]),

                                Section::make('Panduan Chunk')
                                    ->description('Praktik terbaik chunking untuk RAG')
                                    ->icon('heroicon-o-light-bulb')
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('tips')
                                            ->label('')
                                            ->content(
                                                str(
                                                    "**Ukuran chunk ideal:**\n\n" .
                                                    "- 🟢 200–500 token → terbaik\n" .
                                                    "- 🟡 100–200 token → terlalu kecil\n" .
                                                    "- 🔴 > 800 token → terlalu besar\n\n" .
                                                    "---\n\n" .
                                                    "**Tips overlap:**\n\n" .
                                                    "Tambahkan 50–100 token overlap antar chunk untuk menjaga konteks."
                                                )->markdown()->toHtmlString()
                                            ),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
