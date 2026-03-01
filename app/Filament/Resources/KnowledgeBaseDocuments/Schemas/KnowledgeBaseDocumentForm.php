<?php

namespace App\Filament\Resources\KnowledgeBaseDocuments\Schemas;

use App\Enums\DocumentType;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KnowledgeBaseDocumentForm
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

                                Section::make('Informasi Dokumen')
                                    ->description('Judul, tipe, dan sumber dokumen referensi')
                                    ->icon('heroicon-o-book-open')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Judul Dokumen')
                                            ->placeholder('e.g. Panduan Penyakit Newcastle Disease – Ditjen PKH 2023')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Select::make('type')
                                            ->label('Tipe Dokumen')
                                            ->options(DocumentType::class)
                                            ->default(DocumentType::Pdf)
                                            ->required()
                                            ->native(false)
                                            ->helperText('Pilih sesuai format sumber aslinya.'),

                                        TextInput::make('source_url')
                                            ->label('URL Sumber')
                                            ->placeholder('https://ditjenpkh.pertanian.go.id/…')
                                            ->url()
                                            ->suffixIcon('heroicon-m-arrow-top-right-on-square'),
                                    ]),

                                Section::make('Upload File')
                                    ->description('Upload PDF atau TXT — teks dapat diekstrak otomatis setelah upload')
                                    ->icon('heroicon-o-arrow-up-tray')
                                    ->schema([
                                        FileUpload::make('file_path')
                                            ->label('File Dokumen')
                                            ->disk('public')
                                            ->directory('documents')
                                            ->acceptedFileTypes([
                                                'application/pdf',
                                                'text/plain',
                                                'application/msword',
                                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                            ])
                                            ->downloadable()
                                            ->openable()
                                            ->helperText('PDF, TXT, DOC, DOCX. Setelah upload & simpan, gunakan tombol "Ekstrak Teks" di halaman detail.')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Konten Dokumen')
                                    ->description('Teks yang akan digunakan sebagai bahan chunking RAG. Bisa diisi manual atau diekstrak otomatis dari file.')
                                    ->icon('heroicon-o-document-text')
                                    ->collapsed()
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Isi Konten')
                                            ->placeholder("Paste teks di sini, atau gunakan tombol \"Ekstrak Teks\" setelah upload file…\n\nContoh:\nNewcastle Disease (ND) adalah penyakit virus yang sangat menular pada unggas…")
                                            ->rows(18)
                                            ->required(fn ($get) => ! $get('file_path'))
                                            ->helperText(fn ($get) => $get('file_path')
                                                ? 'File sudah diupload — konten dapat diekstrak otomatis dari halaman detail.'
                                                : 'Wajib diisi jika tidak ada file yang diupload.')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Metadata')
                                    ->description('Informasi tambahan dalam format key-value (opsional)')
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
                                    ->description('Apakah dokumen sudah diproses ke vector database?')
                                    ->icon('heroicon-o-cpu-chip')
                                    ->schema([
                                        Toggle::make('is_processed')
                                            ->label('Sudah Diproses / Di-embed')
                                            ->helperText('Aktifkan setelah dokumen berhasil di-chunk dan di-embed ke vector DB.')
                                            ->onColor('success')
                                            ->offColor('warning'),
                                    ]),

                                Section::make('Panduan Alur RAG')
                                    ->description('Langkah kerja dokumen ini dalam sistem')
                                    ->icon('heroicon-o-light-bulb')
                                    ->schema([
                                        Placeholder::make('rag_guide')
                                            ->label('')
                                            ->content(
                                                str(
                                                    "**Alur proses dokumen:**\n\n" .
                                                    "1️⃣ **Upload** → isi konten teks\n\n" .
                                                    "2️⃣ **Chunking** → buat Knowledge Chunks dari dokumen ini\n\n" .
                                                    "3️⃣ **Embedding** → jalankan proses embed tiap chunk ke vector DB\n\n" .
                                                    "4️⃣ **Aktifkan** → set *Sudah Diproses* = ✅\n\n" .
                                                    "---\n\n" .
                                                    "Dokumen yang belum diproses **tidak akan digunakan** chatbot saat menjawab pertanyaan."
                                                )->markdown()->toHtmlString()
                                            ),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
