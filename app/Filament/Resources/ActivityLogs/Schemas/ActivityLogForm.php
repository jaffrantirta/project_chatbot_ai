<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ActivityLogForm
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

                                Section::make('Detail Aktivitas')
                                    ->description('Aksi yang dilakukan dan objek yang terdampak')
                                    ->icon('heroicon-o-bolt')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('action')
                                            ->label('Aksi')
                                            ->disabled()
                                            ->prefixIcon('heroicon-o-arrow-path'),

                                        TextInput::make('subject_id')
                                            ->label('Subject ID')
                                            ->disabled()
                                            ->prefixIcon('heroicon-o-hashtag')
                                            ->placeholder('–'),

                                        TextInput::make('subject_type')
                                            ->label('Tipe Subjek')
                                            ->disabled()
                                            ->placeholder('–')
                                            ->helperText('Model/entitas yang terpengaruh')
                                            ->columnSpanFull(),

                                        Textarea::make('description')
                                            ->label('Deskripsi')
                                            ->disabled()
                                            ->rows(4)
                                            ->placeholder('Tidak ada deskripsi')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Metadata')
                                    ->description('Data tambahan yang dicatat bersama aktivitas ini')
                                    ->icon('heroicon-o-code-bracket')
                                    ->collapsed()
                                    ->schema([
                                        KeyValue::make('metadata')
                                            ->label('')
                                            ->keyLabel('Key')
                                            ->valueLabel('Value')
                                            ->disabled()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ── Sidebar (1/3) ─────────────────────────────────
                        Grid::make(1)
                            ->columnSpan(['default' => 1, 'lg' => 1])
                            ->schema([

                                Section::make('Pengguna & Waktu')
                                    ->description('Siapa yang melakukan dan kapan')
                                    ->icon('heroicon-o-user-circle')
                                    ->schema([
                                        Select::make('user_id')
                                            ->label('Pengguna')
                                            ->relationship('user', 'name')
                                            ->disabled()
                                            ->placeholder('Sistem / Anonim'),

                                        Placeholder::make('created_at_display')
                                            ->label('Waktu Kejadian')
                                            ->content(fn ($record) => $record
                                                ? $record->created_at->translatedFormat('d F Y, H:i:s') .
                                                  ' (' . $record->created_at->diffForHumans() . ')'
                                                : '–'
                                            ),
                                    ]),

                                Section::make('Informasi Teknis')
                                    ->description('Detail jaringan dan browser klien')
                                    ->icon('heroicon-o-computer-desktop')
                                    ->collapsed()
                                    ->schema([
                                        TextInput::make('ip_address')
                                            ->label('IP Address')
                                            ->disabled()
                                            ->prefixIcon('heroicon-o-globe-alt')
                                            ->placeholder('–'),

                                        Textarea::make('user_agent')
                                            ->label('User Agent')
                                            ->disabled()
                                            ->rows(4)
                                            ->placeholder('Tidak tersedia')
                                            ->helperText('Browser / klien yang digunakan'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
