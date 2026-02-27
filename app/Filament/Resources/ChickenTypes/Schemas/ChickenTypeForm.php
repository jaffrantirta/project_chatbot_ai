<?php

namespace App\Filament\Resources\ChickenTypes\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChickenTypeForm
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
                                Section::make('Informasi Jenis Ayam')
                                    ->description('Nama dan karakteristik umum jenis ayam ini')
                                    ->icon('heroicon-o-tag')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nama Jenis')
                                            ->placeholder('e.g. Ayam Kampung, Ayam Bangkok, Pejantan Silang')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Deskripsi')
                                    ->description('Penjelasan singkat mengenai jenis ayam')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        Textarea::make('description')
                                            ->label('Deskripsi')
                                            ->placeholder('Tuliskan gambaran umum jenis ayam ini…')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ── Sidebar (1/3) ─────────────────────────────────
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 1])
                            ->schema([
                                Section::make('Karakteristik')
                                    ->description('Ciri fisik atau keunggulan khusus')
                                    ->icon('heroicon-o-sparkles')
                                    ->schema([
                                        Textarea::make('characteristics')
                                            ->label('Karakteristik')
                                            ->placeholder("- Bulu lebat berwarna coklat\n- Bobot dewasa ±2,5 kg\n- Tahan penyakit")
                                            ->rows(8),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
