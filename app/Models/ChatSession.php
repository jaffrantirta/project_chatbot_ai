<?php

namespace App\Models;

use App\Enums\ChatSessionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    protected $fillable = [
        'user_id',
        'farm_id',
        'chicken_id',
        'title',
        'session_token',
        'status',
        'total_tokens_used',
        'model_used',
    ];

    protected function casts(): array
    {
        return [
            'status'            => ChatSessionStatus::class,
            'total_tokens_used' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function chicken(): BelongsTo
    {
        return $this->belongsTo(Chicken::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'session_id');
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class, 'chat_session_id');
    }
}
