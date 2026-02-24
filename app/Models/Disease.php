<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disease extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'disease_category_id',
        'name',
        'local_name',
        'cause',
        'symptoms',
        'medicine',
        'treatment',
        'prevention',
        'source',
        'reference_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DiseaseCategory::class, 'disease_category_id');
    }

    public function diseaseSymptoms(): HasMany
    {
        return $this->hasMany(DiseaseSymptom::class);
    }

    public function medicines(): BelongsToMany
    {
        return $this->belongsToMany(Medicine::class, 'disease_medicines')
            ->withPivot('notes')
            ->withTimestamps();
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }
}
