<?php

namespace App\Filament\Resources\DiseaseSymptoms\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DiseaseSymptomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 2])
                    ->columnSpanFull()
                    ->schema([

                        // ── Input fields ──────────────────────────────────
                        Section::make('Kata Kunci Gejala')
                            ->description('Tambahkan satu kata kunci yang merepresentasikan gejala penyakit')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Select::make('disease_id')
                                    ->label('Penyakit')
                                    ->relationship('disease', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->placeholder('Pilih penyakit…')
                                    ->helperText('Kata kunci ini akan digunakan untuk mencocokkan gejala yang dilaporkan peternak.'),

                                TextInput::make('keyword')
                                    ->label('Kata Kunci')
                                    ->placeholder('e.g. nafsu makan turun, bulu berdiri, berak hijau')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Gunakan frasa pendek, natural, seperti cara peternak mendeskripsikan gejala.'),
                            ]),

                        // ── Tips sidebar ──────────────────────────────────
                        Section::make('Panduan Penulisan Kata Kunci')
                            ->description('Agar sistem RAG dapat mencocokkan gejala dengan tepat')
                            ->icon('heroicon-o-light-bulb')
                            ->schema([
                                Placeholder::make('tips')
                                    ->label('')
                                    ->content(
                                        str(
                                            "**✅ Kata kunci yang baik:**\n\n" .
                                            "- Singkat dan spesifik\n" .
                                            "- Ditulis seperti ucapan peternak\n" .
                                            "- Satu frasa per entri\n\n" .
                                            "**Contoh:**\n" .
                                            "`nafsu makan turun` ✓\n\n" .
                                            "`ayam tidak mau makan` ✓\n\n" .
                                            "`penurunan produksi telur` ✓\n\n" .
                                            "---\n\n" .
                                            "**❌ Hindari:**\n\n" .
                                            "- Kata terlalu umum: `sakit`, `lemah`\n" .
                                            "- Kalimat panjang\n" .
                                            "- Duplikasi kata kunci pada penyakit yang sama"
                                        )->markdown()->toHtmlString()
                                    ),
                            ]),
                    ]),
            ]);
    }
}
