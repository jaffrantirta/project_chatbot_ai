<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeChunk extends Model
{
    protected $fillable = [
        'document_id',
        'chunk_index',
        'content',
        'token_count',
        'embedding_id',
        'is_embedded',
        'embedding',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_embedded' => 'boolean',
            'embedding'   => 'array',
            'metadata'    => 'array',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseDocument::class, 'document_id');
    }
}
