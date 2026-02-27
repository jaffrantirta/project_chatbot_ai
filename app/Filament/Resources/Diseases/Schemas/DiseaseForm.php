<?php

namespace App\Filament\Resources\Diseases\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DiseaseForm
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

                                Section::make('Identitas Penyakit')
                                    ->description('Nama ilmiah, nama lokal, dan klasifikasi kategori')
                                    ->icon('heroicon-o-document-magnifying-glass')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nama Penyakit')
                                            ->placeholder('e.g. Newcastle Disease')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        TextInput::make('local_name')
                                            ->label('Nama Lokal / Populer')
                                            ->placeholder('e.g. Tetelo, Snot, Berak Kapur')
                                            ->maxLength(255),

                                        Select::make('disease_category_id')
                                            ->label('Kategori Penyakit')
                                            ->relationship('category', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->placeholder('Pilih kategori'),
                                    ]),

                                Section::make('Klinis')
                                    ->description('Penyebab dan gejala yang tampak pada ayam')
                                    ->icon('heroicon-o-beaker')
                                    ->schema([
                                        Textarea::make('cause')
                                            ->label('Penyebab')
                                            ->placeholder('Jelaskan agen penyebab penyakit (virus, bakteri, parasit, dll.)…')
                                            ->rows(4)
                                            ->required()
                                            ->columnSpanFull(),

                                        Textarea::make('symptoms')
                                            ->label('Gejala')
                                            ->placeholder("Gejala yang terlihat pada ayam, contoh:\n- Lesu dan tidak nafsu makan\n- Bulu berdiri\n- Feses berwarna kehijauan")
                                            ->rows(6)
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Penanganan')
                                    ->description('Obat dan cara merawat ayam yang terinfeksi')
                                    ->icon('heroicon-o-heart')
                                    ->schema([
                                        Textarea::make('medicine')
                                            ->label('Obat yang Digunakan')
                                            ->placeholder('Nama obat, dosis, dan frekuensi pemberian…')
                                            ->rows(4)
                                            ->columnSpanFull(),

                                        Textarea::make('treatment')
                                            ->label('Cara Perawatan')
                                            ->placeholder("Langkah-langkah penanganan:\n1. Isolasi ayam yang sakit\n2. Berikan antibiotik…")
                                            ->rows(6)
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Pencegahan')
                                    ->description('Langkah-langkah untuk mencegah penyebaran penyakit')
                                    ->icon('heroicon-o-shield-check')
                                    ->collapsed()
                                    ->schema([
                                        Textarea::make('prevention')
                                            ->label('Pencegahan')
                                            ->placeholder("Program vaksinasi dan biosekuriti:\n- Vaksinasi rutin setiap …\n- Desinfeksi kandang setiap …")
                                            ->rows(6)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ── Sidebar (1/3) ─────────────────────────────────
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 1])
                            ->schema([

                                Section::make('Status Publikasi')
                                    ->description('Aktifkan agar penyakit dapat dikenali oleh chatbot')
                                    ->icon('heroicon-o-check-badge')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Aktif di Knowledge Base')
                                            ->helperText('Penyakit nonaktif tidak akan digunakan dalam proses diagnosis chatbot.')
                                            ->default(true)
                                            ->onColor('success')
                                            ->offColor('danger'),
                                    ]),

                                Section::make('Kata Kunci Gejala')
                                    ->description('Keyword untuk pencocokan gejala (RAG similarity search)')
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->schema([
                                        Repeater::make('diseaseSymptoms')
                                            ->relationship('diseaseSymptoms')
                                            ->label('')
                                            ->schema([
                                                TextInput::make('keyword')
                                                    ->label('Kata Kunci')
                                                    ->placeholder('e.g. nafsu makan turun')
                                                    ->required()
                                                    ->maxLength(255),
                                            ])
                                            ->addActionLabel('+ Tambah Kata Kunci')
                                            ->defaultItems(0)
                                            ->reorderable(false)
                                            ->collapsible()
                                            ->grid(1),
                                    ]),

                                Section::make('Obat Terkait')
                                    ->description('Pilih obat yang relevan dari daftar formularium')
                                    ->icon('heroicon-o-beaker')
                                    ->schema([
                                        Select::make('medicines')
                                            ->relationship('medicines', 'name')
                                            ->label('')
                                            ->multiple()
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Pilih satu atau lebih obat…'),
                                    ]),

                                Section::make('Referensi')
                                    ->description('Sumber dan tautan dokumentasi ilmiah')
                                    ->icon('heroicon-o-link')
                                    ->collapsed()
                                    ->schema([
                                        TextInput::make('source')
                                            ->label('Sumber')
                                            ->placeholder('e.g. Direktorat Kesehatan Hewan')
                                            ->maxLength(255),

                                        TextInput::make('reference_url')
                                            ->label('URL Referensi')
                                            ->placeholder('https://…')
                                            ->url()
                                            ->suffixIcon('heroicon-m-arrow-top-right-on-square'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
