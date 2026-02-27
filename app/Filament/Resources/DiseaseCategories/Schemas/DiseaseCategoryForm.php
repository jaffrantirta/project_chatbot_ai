<?php

namespace App\Filament\Resources\DiseaseCategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DiseaseCategoryForm
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
                                Section::make('Informasi Kategori')
                                    ->description('Pengelompokan penyakit berdasarkan agen penyebabnya')
                                    ->icon('heroicon-o-folder-open')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nama Kategori')
                                            ->placeholder('e.g. Virus, Bakteri, Parasit, Mikal/Fungi')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->helperText('Nama kategori harus unik dan mencerminkan jenis agen penyebab penyakit.')
                                            ->columnSpanFull(),

                                        Textarea::make('description')
                                            ->label('Deskripsi')
                                            ->placeholder('Jelaskan karakteristik umum kategori ini, cara penyebaran, dan contoh penyakitnya…')
                                            ->rows(5)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ── Sidebar (1/3) ─────────────────────────────────
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 1])
                            ->schema([
                                Section::make('Panduan')
                                    ->description('Referensi kategori yang umum digunakan')
                                    ->icon('heroicon-o-light-bulb')
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('tips')
                                            ->label('')
                                            ->content(
                                                str(
                                                    "**Kategori Standar:**\n\n" .
                                                    "🦠 **Virus** — Newcastle, AI, Marek's\n\n" .
                                                    "🧫 **Bakteri** — Kolera, CRD, Salmonella\n\n" .
                                                    "🪱 **Parasit** — Coccidiosis, Cacingan\n\n" .
                                                    "🍄 **Mikal/Fungi** — Aspergillosis"
                                                )->markdown()->toHtmlString()
                                            ),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
