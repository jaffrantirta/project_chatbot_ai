<?php

namespace App\Models;

use App\Enums\HealthStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HealthRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'chicken_id',
        'farm_id',
        'recorded_by',
        'chat_session_id',
        'disease_id',
        'status',
        'symptoms_reported',
        'diagnosis_result',
        'treatment_given',
        'medicine_given',
        'vet_consulted',
        'record_date',
        'follow_up_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status'        => HealthStatus::class,
            'vet_consulted' => 'boolean',
            'record_date'   => 'date',
            'follow_up_date'=> 'date',
        ];
    }

    public function chicken(): BelongsTo
    {
        return $this->belongsTo(Chicken::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'chat_session_id');
    }

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }
}
