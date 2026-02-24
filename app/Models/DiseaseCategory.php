<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiseaseCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function diseases(): HasMany
    {
        return $this->hasMany(Disease::class);
    }
}
