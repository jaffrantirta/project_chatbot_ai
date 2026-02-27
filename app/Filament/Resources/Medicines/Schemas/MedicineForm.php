<?php

namespace App\Filament\Resources\Medicines\Schemas;

use App\Enums\MedicineType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MedicineForm
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

                                Section::make('Informasi Obat')
                                    ->description('Nama, jenis, dan klasifikasi produk')
                                    ->icon('heroicon-o-beaker')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nama Obat / Vaksin')
                                            ->placeholder('e.g. Amoxicilin, ND-IB Vaccine, Vita Chick')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Select::make('type')
                                            ->label('Tipe Produk')
                                            ->options(MedicineType::class)
                                            ->default(MedicineType::Lainnya)
                                            ->required()
                                            ->native(false)
                                            ->helperText('Pilih tipe yang paling sesuai dengan produk ini.'),

                                        Select::make('diseases')
                                            ->label('Digunakan untuk Penyakit')
                                            ->relationship('diseases', 'name')
                                            ->multiple()
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Pilih penyakit yang ditangani…')
                                            ->helperText('Opsional. Bisa diatur juga dari halaman data penyakit.'),
                                    ]),

                                Section::make('Deskripsi')
                                    ->description('Penjelasan umum mengenai produk ini')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        Textarea::make('description')
                                            ->label('Deskripsi')
                                            ->placeholder('Kandungan aktif, fungsi utama, dan keterangan produk lainnya…')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Petunjuk Penggunaan')
                                    ->description('Panduan dosis dan cara pemberian ke ayam')
                                    ->icon('heroicon-o-clipboard-document-list')
                                    ->columns(2)
                                    ->schema([
                                        Textarea::make('dosage')
                                            ->label('Aturan Dosis')
                                            ->placeholder("Contoh:\n- Ayam dewasa: 10 mg/kg BB\n- Ayam muda: 5 mg/kg BB\n- Durasi: 5–7 hari")
                                            ->rows(5),

                                        Textarea::make('administration')
                                            ->label('Cara Pemberian')
                                            ->placeholder("Contoh:\n- Larutkan dalam air minum\n- Berikan pagi hari sebelum pakan\n- Hindari kontak langsung dengan kulit")
                                            ->rows(5),
                                    ]),
                            ]),

                        // ── Sidebar (1/3) ─────────────────────────────────
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 1])
                            ->schema([

                                Section::make('Status')
                                    ->description('Aktifkan agar obat ini tersedia untuk direkomendasikan chatbot')
                                    ->icon('heroicon-o-check-badge')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Obat Aktif')
                                            ->helperText('Obat nonaktif tidak akan disertakan dalam rekomendasi diagnosis.')
                                            ->default(true)
                                            ->onColor('success')
                                            ->offColor('danger'),
                                    ]),

                            ]),
                    ]),
            ]);
    }
}
