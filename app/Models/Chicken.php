<?php

namespace App\Models;

use App\Enums\ChickenGender;
use App\Enums\ChickenStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chicken extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'farm_id',
        'chicken_type_id',
        'code',
        'name',
        'gender',
        'birth_date',
        'age_weeks',
        'weight_kg',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'gender'     => ChickenGender::class,
            'status'     => ChickenStatus::class,
            'birth_date' => 'date',
            'weight_kg'  => 'decimal:2',
        ];
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function chickenType(): BelongsTo
    {
        return $this->belongsTo(ChickenType::class);
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }
}
