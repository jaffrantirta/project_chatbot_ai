<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChickenType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'characteristics',
    ];

    public function chickens(): HasMany
    {
        return $this->hasMany(Chicken::class);
    }
}
