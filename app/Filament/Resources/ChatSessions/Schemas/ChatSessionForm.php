<?php

namespace App\Filament\Resources\ChatSessions\Schemas;

use App\Enums\ChatSessionStatus;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChatSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->columnSpanFull()
                    ->schema([

                        // ── Main (2/3) ─────────────────────────────────────
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 2])
                            ->schema([

                                Section::make('Identitas Sesi')
                                    ->description('Judul, token, dan model AI yang digunakan')
                                    ->icon('heroicon-o-chat-bubble-left-right')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Judul Sesi')
                                            ->placeholder('e.g. Konsultasi Penyakit Ayam Kandang A')
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        TextInput::make('session_token')
                                            ->label('Token Sesi')
                                            ->placeholder('Otomatis dibuat jika dikosongkan')
                                            ->maxLength(255)
                                            ->helperText('UUID unik sesi ini.')
                                            ->prefixIcon('heroicon-o-key'),

                                        TextInput::make('model_used')
                                            ->label('Model AI')
                                            ->default('gpt-4o-mini')
                                            ->required()
                                            ->maxLength(100)
                                            ->prefixIcon('heroicon-o-cpu-chip')
                                            ->helperText('Nama model OpenAI, e.g. gpt-4o, gpt-4o-mini.'),
                                    ]),

                                Section::make('Konteks Peternakan')
                                    ->description('Kaitkan sesi ini dengan kandang dan ayam tertentu')
                                    ->icon('heroicon-o-home-modern')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('user_id')
                                            ->label('Pengguna')
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->placeholder('Pilih pengguna…')
                                            ->columnSpanFull(),

                                        Select::make('farm_id')
                                            ->label('Kandang')
                                            ->relationship('farm', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Tidak dikaitkan')
                                            ->nullable(),

                                        Select::make('chicken_id')
                                            ->label('Ayam')
                                            ->relationship('chicken', 'code')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Tidak dikaitkan')
                                            ->nullable(),
                                    ]),
                            ]),

                        // ── Sidebar (1/3) ──────────────────────────────────
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 1])
                            ->schema([

                                Section::make('Status Sesi')
                                    ->description('Status dan statistik penggunaan token')
                                    ->icon('heroicon-o-signal')
                                    ->schema([
                                        Select::make('status')
                                            ->label('Status')
                                            ->options(ChatSessionStatus::class)
                                            ->default(ChatSessionStatus::Active)
                                            ->required()
                                            ->native(false),

                                        TextInput::make('total_tokens_used')
                                            ->label('Total Token Terpakai')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->suffix('tokens')
                                            ->helperText('Diperbarui otomatis setiap pesan.'),
                                    ]),

                                Section::make('Info')
                                    ->description('Cara kerja sesi chat')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Placeholder::make('info')
                                            ->label('')
                                            ->content(
                                                str(
                                                    "**Alur sesi chat:**\n\n" .
                                                    "1. 🆕 Buat sesi → pilih kandang & ayam\n" .
                                                    "2. 💬 Klik **Mulai Chat** untuk membuka antarmuka percakapan\n" .
                                                    "3. 🤖 AI menjawab berbasis knowledge base RAG\n" .
                                                    "4. ✅ Tutup sesi setelah selesai\n\n" .
                                                    "---\n\n" .
                                                    "**Model tersedia:**\n\n" .
                                                    "- `gpt-4o-mini` → cepat & hemat\n" .
                                                    "- `gpt-4o` → lebih akurat"
                                                )->markdown()->toHtmlString()
                                            ),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
