<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfusionMatrixTest extends Model
{
    protected $fillable = [
        'test_name',
        'tested_by',
        'total_samples',
        'true_positive',
        'true_negative',
        'false_positive',
        'false_negative',
        'accuracy',
        'precision_score',
        'recall_score',
        'f1_score',
        'notes',
        'tested_at',
    ];

    protected function casts(): array
    {
        return [
            'tested_at'       => 'datetime',
            'accuracy'        => 'decimal:4',
            'precision_score' => 'decimal:4',
            'recall_score'    => 'decimal:4',
            'f1_score'        => 'decimal:4',
        ];
    }

    public function tester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tested_by');
    }
}
