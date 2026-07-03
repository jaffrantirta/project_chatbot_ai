<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Write an activity log entry. Never throws — a logging failure
     * must not break the action being logged.
     */
    public static function record(
        string $action,
        ?Model $subject = null,
        ?string $description = null,
        array $metadata = [],
        ?int $userId = null,
    ): ?self {
        try {
            return self::create([
                'user_id'      => $userId ?? auth()->id(),
                'action'       => $action,
                'subject_type' => $subject?->getMorphClass(),
                'subject_id'   => $subject?->getKey(),
                'description'  => $description,
                'ip_address'   => request()?->ip(),
                'user_agent'   => request()?->userAgent(),
                'metadata'     => $metadata ?: null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal menulis activity log', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
