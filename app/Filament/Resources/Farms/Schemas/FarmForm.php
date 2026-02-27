<?php

namespace App\Filament\Resources\Farms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FarmForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── Left column (main content) ──────────────────────────────
                Grid::make(['default' => 1, 'lg' => 3])
                    ->columnSpanFull()
                    ->schema([
                        // ── Main content (2/3 width) ─────────────────────
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 2])
                            ->schema([
                                Section::make('Informasi Kandang')
                                    ->description('Data utama kandang peternakan')
                                    ->icon('heroicon-o-home')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nama Kandang')
                                            ->placeholder('e.g. Kandang A – Blok 1')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        Select::make('user_id')
                                            ->label('Pemilik')
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->placeholder('Pilih pemilik kandang'),
                                        TextInput::make('total_chickens')
                                            ->label('Jumlah Ayam')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->suffix('ekor')
                                            ->required(),
                                    ]),
                                Section::make('Lokasi')
                                    ->description('Alamat dan wilayah kandang')
                                    ->icon('heroicon-o-map-pin')
                                    ->columns(2)
                                    ->schema([
                                        Textarea::make('address')
                                            ->label('Alamat Lengkap')
                                            ->placeholder('Jl. Raya Peternakan No. 1, RT 02/RW 03')
                                            ->rows(3)
                                            ->required()
                                            ->columnSpanFull(),
                                        TextInput::make('province')
                                            ->label('Provinsi')
                                            ->placeholder('e.g. Jawa Tengah')
                                            ->maxLength(100),
                                        TextInput::make('city')
                                            ->label('Kota / Kabupaten')
                                            ->placeholder('e.g. Kabupaten Blora')
                                            ->maxLength(100),
                                    ]),
                                Section::make('Catatan')
                                    ->description('Informasi tambahan mengenai kandang')
                                    ->icon('heroicon-o-document-text')
                                    ->collapsed()
                                    ->schema([
                                        Textarea::make('notes')
                                            ->label('Catatan')
                                            ->placeholder('Tulis catatan tambahan di sini…')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        // ── Sidebar (1/3 width) ──────────────────────────
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 1])
                            ->schema([
                                Section::make('Status')
                                    ->description('Aktifkan atau nonaktifkan kandang')
                                    ->icon('heroicon-o-check-badge')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Kandang Aktif')
                                            ->helperText('Kandang yang tidak aktif tidak akan muncul pada fitur monitoring.')
                                            ->default(true)
                                            ->onColor('success')
                                            ->offColor('danger'),
                                    ]),
                                Section::make('Data Usaha')
                                    ->description('Nomor legalitas usaha peternakan')
                                    ->icon('heroicon-o-building-office-2')
                                    ->schema([
                                        TextInput::make('nib')
                                            ->label('NIB')
                                            ->placeholder('xxxx-xxxx-xxxx-xxxx')
                                            ->helperText('Nomor Induk Berusaha dari OSS')
                                            ->maxLength(100),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
