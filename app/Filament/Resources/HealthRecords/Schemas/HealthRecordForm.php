<?php

namespace App\Filament\Resources\HealthRecords\Schemas;

use App\Enums\HealthStatus;
use App\Models\Chicken;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HealthRecordForm
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

                                Section::make('Identitas Pemeriksaan')
                                    ->description('Ayam, kandang, dan jadwal pemeriksaan')
                                    ->icon('heroicon-o-identification')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('chicken_id')
                                            ->label('Ayam')
                                            ->options(fn () => Chicken::with('farm')
                                                ->get()
                                                ->mapWithKeys(fn ($c) => [
                                                    $c->id => $c->code . ($c->name ? " — {$c->name}" : '') . " ({$c->farm?->name})",
                                                ]))
                                            ->searchable()
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $chicken = Chicken::find($state);
                                                    $set('farm_id', $chicken?->farm_id);
                                                }
                                            })
                                            ->placeholder('Cari kode atau nama ayam…')
                                            ->helperText('Memilih ayam akan mengisi kandang secara otomatis.')
                                            ->columnSpanFull(),

                                        Select::make('farm_id')
                                            ->label('Kandang')
                                            ->relationship('farm', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->placeholder('Terisi otomatis dari ayam')
                                            ->helperText('Dapat diubah manual jika diperlukan.'),

                                        Select::make('recorded_by')
                                            ->label('Dicatat oleh')
                                            ->relationship('recordedBy', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->placeholder('Pilih petugas…'),

                                        DatePicker::make('record_date')
                                            ->label('Tanggal Pemeriksaan')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('d M Y')
                                            ->default(now()),

                                        DatePicker::make('follow_up_date')
                                            ->label('Jadwal Kontrol')
                                            ->native(false)
                                            ->displayFormat('d M Y')
                                            ->minDate(fn ($get) => $get('record_date'))
                                            ->helperText('Tanggal pemeriksaan lanjutan jika diperlukan.'),
                                    ]),

                                Section::make('Gejala yang Dilaporkan')
                                    ->description('Kondisi dan keluhan yang diamati peternak')
                                    ->icon('heroicon-o-eye')
                                    ->schema([
                                        Textarea::make('symptoms_reported')
                                            ->label('Gejala')
                                            ->placeholder("Deskripsikan gejala yang terlihat, contoh:\n- Ayam terlihat lesu dan tidak aktif\n- Nafsu makan turun sejak 2 hari lalu\n- Feses berwarna kehijauan dan encer")
                                            ->rows(5)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Diagnosis')
                                    ->description('Hasil analisis dan penyakit yang teridentifikasi')
                                    ->icon('heroicon-o-document-magnifying-glass')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('disease_id')
                                            ->label('Penyakit Terdiagnosis')
                                            ->relationship('disease', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Belum teridentifikasi')
                                            ->helperText('Kosongkan jika belum dapat ditentukan.')
                                            ->columnSpanFull(),

                                        Textarea::make('diagnosis_result')
                                            ->label('Hasil Diagnosis')
                                            ->placeholder('Penjelasan hasil diagnosis, tingkat keparahan, dan kesimpulan pemeriksaan…')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Penanganan')
                                    ->description('Tindakan perawatan dan obat yang diberikan')
                                    ->icon('heroicon-o-heart')
                                    ->columns(2)
                                    ->schema([
                                        Textarea::make('treatment_given')
                                            ->label('Tindakan Perawatan')
                                            ->placeholder("Langkah penanganan yang dilakukan:\n- Isolasi dari kandang utama\n- Pembersihan kandang\n- Pemberian larutan elektrolit")
                                            ->rows(5),

                                        Textarea::make('medicine_given')
                                            ->label('Obat yang Diberikan')
                                            ->placeholder("Nama obat, dosis, dan frekuensi:\n- Amoxicilin 10 mg/kg BB, 2x sehari\n- Vitamin C 500 mg/liter air")
                                            ->rows(5),
                                    ]),

                                Section::make('Catatan Tambahan')
                                    ->description('Informasi lain yang perlu didokumentasikan')
                                    ->icon('heroicon-o-document-text')
                                    ->collapsed()
                                    ->schema([
                                        Textarea::make('notes')
                                            ->label('Catatan')
                                            ->placeholder('Riwayat vaksin, kondisi kandang, kontak dengan hewan lain, dll…')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ── Sidebar (1/3) ─────────────────────────────────
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 1])
                            ->schema([

                                Section::make('Status Kesehatan')
                                    ->description('Kondisi ayam saat ini')
                                    ->icon('heroicon-o-heart')
                                    ->schema([
                                        Select::make('status')
                                            ->label('Status')
                                            ->options(HealthStatus::class)
                                            ->default(HealthStatus::Sakit)
                                            ->required()
                                            ->native(false)
                                            ->helperText('Perbarui status setiap kali kondisi ayam berubah.'),
                                    ]),

                                Section::make('Konsultasi & Sesi')
                                    ->description('Rekam jejak konsultasi digital')
                                    ->icon('heroicon-o-chat-bubble-left-right')
                                    ->schema([
                                        Toggle::make('vet_consulted')
                                            ->label('Sudah Konsultasi Dokter')
                                            ->helperText('Centang jika ayam sudah diperiksa dokter hewan.')
                                            ->onColor('success')
                                            ->offColor('gray'),

                                        Select::make('chat_session_id')
                                            ->label('Sesi Chat Terkait')
                                            ->relationship('chatSession', 'session_token')
                                            ->searchable()
                                            ->placeholder('Tidak ada sesi terkait')
                                            ->helperText('Hubungkan dengan sesi chatbot jika ada.'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
