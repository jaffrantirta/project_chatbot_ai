<?php

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class KnowledgeBaseDocument extends Model
{
    protected $fillable = [
        'title',
        'type',
        'content',
        'file_path',
        'source_url',
        'is_processed',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'type'         => DocumentType::class,
            'is_processed' => 'boolean',
            'metadata'     => 'array',
        ];
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(KnowledgeChunk::class, 'document_id');
    }

    protected static function booted(): void
    {
        static::deleting(function (self $document) {
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
        });
    }
}
