<?php

namespace App\Models;

use App\Enums\MessageRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'session_id',
        'role',
        'content',
        'tokens_used',
        'retrieved_chunk_ids',
        'disease_id',
        'confidence_score',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'role'                => MessageRole::class,
            'retrieved_chunk_ids' => 'array',
            'metadata'            => 'array',
            'confidence_score'    => 'decimal:2',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'session_id');
    }

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }
}
