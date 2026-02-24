<?php

namespace App\Models;

use App\Enums\MedicineType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Medicine extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
        'dosage',
        'administration',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type'      => MedicineType::class,
            'is_active' => 'boolean',
        ];
    }

    public function diseases(): BelongsToMany
    {
        return $this->belongsToMany(Disease::class, 'disease_medicines')
            ->withPivot('notes')
            ->withTimestamps();
    }
}
