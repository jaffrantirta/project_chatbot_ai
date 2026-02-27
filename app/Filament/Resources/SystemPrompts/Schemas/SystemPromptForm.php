<?php

namespace App\Filament\Resources\SystemPrompts\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SystemPromptForm
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

                                Section::make('Identitas Prompt')
                                    ->description('Nama, versi, dan pemilik prompt ini')
                                    ->icon('heroicon-o-identification')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nama Prompt')
                                            ->placeholder('e.g. RAG Chatbot v2 — Fokus Penyakit Ayam')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        TextInput::make('version')
                                            ->label('Versi')
                                            ->placeholder('e.g. v1.0, v2.3-beta')
                                            ->maxLength(50)
                                            ->prefixIcon('heroicon-o-tag'),

                                        Select::make('created_by')
                                            ->label('Dibuat Oleh')
                                            ->relationship('creator', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->placeholder('Pilih pengguna…')
                                            ->prefixIcon('heroicon-o-user'),
                                    ]),

                                Section::make('Isi System Prompt')
                                    ->description('Instruksi lengkap yang dikirim ke model AI sebagai konteks awal')
                                    ->icon('heroicon-o-command-line')
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Konten Prompt')
                                            ->placeholder(
                                                "Kamu adalah asisten AI untuk monitoring kesehatan ayam...\n\n" .
                                                "Gunakan hanya informasi dari knowledge base yang disediakan.\n" .
                                                "Jawab dalam Bahasa Indonesia yang ramah dan profesional."
                                            )
                                            ->rows(18)
                                            ->required()
                                            ->helperText('Tulis instruksi sistem secara lengkap. Semakin spesifik, semakin baik respons AI.')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Catatan Internal')
                                    ->description('Dokumentasi perubahan dan alasan update prompt')
                                    ->icon('heroicon-o-clipboard-document-list')
                                    ->collapsed()
                                    ->schema([
                                        Textarea::make('notes')
                                            ->label('Catatan')
                                            ->placeholder(
                                                "Contoh:\n" .
                                                "- v2.0: Ditambahkan instruksi RAG retrieval\n" .
                                                "- v2.1: Perbaikan tone menjadi lebih ramah\n" .
                                                "- v2.2: Tambah konteks penyakit ayam spesifik"
                                            )
                                            ->rows(5)
                                            ->helperText('Gunakan untuk mencatat changelog atau alasan perubahan prompt.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ── Sidebar (1/3) ─────────────────────────────────
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 1])
                            ->schema([

                                Section::make('Status Prompt')
                                    ->description('Hanya satu prompt yang boleh aktif pada satu waktu')
                                    ->icon('heroicon-o-bolt')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Aktifkan Prompt Ini')
                                            ->helperText('Prompt aktif akan digunakan di seluruh sesi chatbot.')
                                            ->onColor('success')
                                            ->offColor('gray'),

                                        Placeholder::make('active_warning')
                                            ->label('')
                                            ->content(
                                                str(
                                                    "⚠️ **Perhatian:**\n\n" .
                                                    "Pastikan hanya **satu** prompt yang aktif.\n\n" .
                                                    "Jika lebih dari satu diaktifkan, sistem akan menggunakan prompt pertama yang ditemukan."
                                                )->markdown()->toHtmlString()
                                            ),
                                    ]),

                                Section::make('Panduan Penulisan Prompt')
                                    ->description('Best practice system prompt untuk RAG chatbot')
                                    ->icon('heroicon-o-light-bulb')
                                    ->schema([
                                        Placeholder::make('tips')
                                            ->label('')
                                            ->content(
                                                str(
                                                    "**Struktur ideal:**\n\n" .
                                                    "1. 🎭 **Peran** — Definisikan siapa AI ini\n" .
                                                    "2. 📚 **Konteks** — Domain & knowledge base\n" .
                                                    "3. 🎯 **Tugas** — Apa yang harus dilakukan\n" .
                                                    "4. 🚫 **Batasan** — Apa yang tidak boleh\n" .
                                                    "5. 🗣️ **Gaya** — Bahasa & tone respons\n\n" .
                                                    "---\n\n" .
                                                    "**Tips token:**\n\n" .
                                                    "- 🟢 < 500 token → efisien\n" .
                                                    "- 🟡 500–1000 token → wajar\n" .
                                                    "- 🔴 > 1000 token → pertimbangkan ulang"
                                                )->markdown()->toHtmlString()
                                            ),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
