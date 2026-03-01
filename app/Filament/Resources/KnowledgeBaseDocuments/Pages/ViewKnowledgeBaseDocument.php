<?php

namespace App\Filament\Resources\KnowledgeBaseDocuments\Pages;

use App\Filament\Resources\KnowledgeBaseDocuments\KnowledgeBaseDocumentResource;
use App\Jobs\EmbedKnowledgeChunkJob;
use App\Services\EmbeddingService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewKnowledgeBaseDocument extends ViewRecord
{
    protected static string $resource = KnowledgeBaseDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ── 0. Extract text from uploaded file ────────────────────────
            Action::make('extract_text')
                ->label('Ekstrak Teks')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->visible(fn () => filled($this->record->file_path))
                ->requiresConfirmation()
                ->modalHeading('Ekstrak Teks dari File')
                ->modalDescription('Konten dokumen akan diperbarui dengan teks yang diekstrak dari file yang diupload. Teks lama akan ditimpa.')
                ->modalSubmitActionLabel('Ya, Ekstrak Teks')
                ->action(function () {
                    $filePath  = storage_path('app/public/' . $this->record->file_path);
                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                    if (! file_exists($filePath)) {
                        Notification::make()
                            ->danger()
                            ->title('File tidak ditemukan')
                            ->body('Pastikan file sudah diupload dengan benar.')
                            ->send();
                        return;
                    }

                    $text = match ($extension) {
                        'pdf'  => $this->extractPdf($filePath),
                        'txt'  => file_get_contents($filePath),
                        default => null,
                    };

                    if ($text === null) {
                        Notification::make()
                            ->warning()
                            ->title('Format tidak didukung untuk ekstraksi otomatis')
                            ->body('Silakan salin teks secara manual dari file DOC/DOCX ke kolom Konten Dokumen.')
                            ->send();
                        return;
                    }

                    $text = trim($text);

                    if (strlen($text) < 20) {
                        Notification::make()
                            ->warning()
                            ->title('Teks terlalu pendek atau kosong')
                            ->body('PDF mungkin berisi gambar/scan dan tidak bisa diekstrak secara otomatis. Gunakan OCR terlebih dahulu.')
                            ->send();
                        return;
                    }

                    $this->record->update(['content' => $text]);

                    Notification::make()
                        ->success()
                        ->title('Teks berhasil diekstrak (' . number_format(strlen($text)) . ' karakter)')
                        ->body('Konten dokumen telah diperbarui. Langkah berikutnya: klik "Buat Chunks".')
                        ->send();
                }),

            // ── 1. Auto-chunk ─────────────────────────────────────────────
            Action::make('chunk')
                ->label('Buat Chunks')
                ->icon('heroicon-o-scissors')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Buat Chunks Otomatis')
                ->modalDescription('Dokumen akan dipecah menjadi chunks. Chunks lama akan dihapus dan embedding sebelumnya akan hilang. Lanjutkan?')
                ->modalSubmitActionLabel('Ya, Buat Chunks')
                ->visible(fn () => filled($this->record->content))
                ->action(function (EmbeddingService $service) {
                    try {
                        $count = $service->createChunksForDocument($this->record);

                        Notification::make()
                            ->success()
                            ->title("{$count} chunks berhasil dibuat")
                            ->body('Langkah berikutnya: klik "Embed Chunks" untuk menghasilkan vector embedding.')
                            ->send();
                    } catch (\RuntimeException $e) {
                        Notification::make()
                            ->danger()
                            ->title('Gagal membuat chunks')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),

            // ── 2. Embed unembedded chunks ────────────────────────────────
            Action::make('embed')
                ->label('Embed Chunks')
                ->icon('heroicon-o-cpu-chip')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Embed Semua Chunks')
                ->modalDescription(fn () => 'Akan mem-proses ' . $this->record->chunks()->where('is_embedded', false)->count() . ' chunk yang belum di-embed melalui OpenAI Embeddings API.')
                ->modalSubmitActionLabel('Ya, Mulai Embed')
                ->visible(fn () => $this->record->chunks()->where('is_embedded', false)->exists())
                ->action(function () {
                    $chunks = $this->record->chunks()->where('is_embedded', false)->get();

                    foreach ($chunks as $chunk) {
                        EmbedKnowledgeChunkJob::dispatch($chunk);
                    }

                    Notification::make()
                        ->success()
                        ->title("{$chunks->count()} chunk dikirim untuk embedding")
                        ->body('Proses berjalan di background. Refresh halaman untuk melihat status terbaru.')
                        ->send();
                }),

            // ── 3. Re-embed all ───────────────────────────────────────────
            Action::make('re_embed')
                ->label('Re-embed')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Re-embed Semua Chunks')
                ->modalDescription('Semua chunks (termasuk yang sudah di-embed) akan diproses ulang.')
                ->modalSubmitActionLabel('Ya, Re-embed')
                ->visible(fn () => $this->record->chunks()->where('is_embedded', true)->exists())
                ->action(function () {
                    $chunks = $this->record->chunks()->get();

                    foreach ($chunks as $chunk) {
                        $chunk->update(['is_embedded' => false, 'embedding' => null]);
                        EmbedKnowledgeChunkJob::dispatch($chunk);
                    }

                    Notification::make()
                        ->success()
                        ->title("{$chunks->count()} chunk dijadwalkan untuk re-embed")
                        ->send();
                }),

            EditAction::make()->label('Edit'),
            DeleteAction::make()->label('Hapus'),
        ];
    }

    private function extractPdf(string $filePath): string
    {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile($filePath);

        return $pdf->getText();
    }
}
