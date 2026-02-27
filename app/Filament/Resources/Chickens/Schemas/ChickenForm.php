<?php

namespace App\Filament\Resources\Chickens\Schemas;

use App\Enums\ChickenGender;
use App\Enums\ChickenStatus;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChickenForm
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

                                Section::make('Identitas Ayam')
                                    ->description('Data registrasi dan kepemilikan ayam')
                                    ->icon('heroicon-o-identification')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('code')
                                            ->label('Kode Ayam')
                                            ->placeholder('e.g. AYM-001')
                                            ->helperText('Kode unik sebagai identitas ayam. Tidak bisa diubah setelah disimpan.')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(100)
                                            ->columnSpanFull(),

                                        TextInput::make('name')
                                            ->label('Nama / Label')
                                            ->placeholder('e.g. Si Jago, Induk A')
                                            ->maxLength(255),

                                        Select::make('gender')
                                            ->label('Jenis Kelamin')
                                            ->options(ChickenGender::class)
                                            ->default(ChickenGender::Jantan)
                                            ->required()
                                            ->native(false),

                                        Select::make('farm_id')
                                            ->label('Kandang')
                                            ->relationship('farm', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->placeholder('Pilih kandang'),

                                        Select::make('chicken_type_id')
                                            ->label('Jenis Ayam')
                                            ->relationship('chickenType', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->placeholder('Pilih jenis ayam'),
                                    ]),

                                Section::make('Data Fisik')
                                    ->description('Informasi bobot, usia, dan tanggal lahir')
                                    ->icon('heroicon-o-scale')
                                    ->columns(2)
                                    ->schema([
                                        DatePicker::make('birth_date')
                                            ->label('Tanggal Lahir')
                                            ->maxDate(now())
                                            ->native(false)
                                            ->displayFormat('d M Y')
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $set('age_weeks', (int) Carbon::parse($state)->diffInWeeks(now()));
                                                }
                                            }),

                                        TextInput::make('age_weeks')
                                            ->label('Usia (Minggu)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->suffix('minggu')
                                            ->helperText('Terisi otomatis saat tanggal lahir dipilih.'),

                                        TextInput::make('weight_kg')
                                            ->label('Berat Badan')
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->suffix('kg'),
                                    ]),

                                Section::make('Catatan')
                                    ->description('Keterangan tambahan tentang ayam ini')
                                    ->icon('heroicon-o-document-text')
                                    ->collapsed()
                                    ->schema([
                                        Textarea::make('notes')
                                            ->label('Catatan')
                                            ->placeholder('Riwayat vaksin, ciri khusus, atau catatan lainnya…')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ── Sidebar (1/3) ─────────────────────────────────
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 1])
                            ->schema([
                                Section::make('Status Kesehatan')
                                    ->description('Kondisi terkini ayam')
                                    ->icon('heroicon-o-heart')
                                    ->schema([
                                        Select::make('status')
                                            ->label('Status')
                                            ->options(ChickenStatus::class)
                                            ->default(ChickenStatus::Sehat)
                                            ->required()
                                            ->native(false)
                                            ->helperText('Perbarui status setiap ada perubahan kondisi.'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
