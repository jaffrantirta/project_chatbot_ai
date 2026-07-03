<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Generic observer that writes an ActivityLog entry for every
 * create / update / delete on the models it is registered for
 * (see AppServiceProvider::boot).
 */
class ActivityLogObserver
{
    /**
     * Attributes that must never be written to the log.
     */
    private const HIDDEN_ATTRIBUTES = [
        'password',
        'remember_token',
        'embedding',
    ];

    public function created(Model $model): void
    {
        ActivityLog::record(
            action: 'create',
            subject: $model,
            description: $this->describe('membuat', $model),
        );
    }

    public function updated(Model $model): void
    {
        $changes = collect($model->getChanges())
            ->except(array_merge(self::HIDDEN_ATTRIBUTES, ['updated_at']))
            ->keys()
            ->all();

        // Nothing meaningful changed (e.g. only updated_at was touched)
        if (empty($changes)) {
            return;
        }

        ActivityLog::record(
            action: 'update',
            subject: $model,
            description: $this->describe('mengubah', $model),
            metadata: ['changed_fields' => $changes],
        );
    }

    public function deleted(Model $model): void
    {
        ActivityLog::record(
            action: 'delete',
            subject: $model,
            description: $this->describe('menghapus', $model),
        );
    }

    private function describe(string $verb, Model $model): string
    {
        $name  = class_basename($model);
        $label = $model->getAttribute('name')
            ?? $model->getAttribute('title')
            ?? $model->getAttribute('code')
            ?? "#{$model->getKey()}";

        $actor = auth()->user()?->name ?? 'Sistem';

        return "{$actor} {$verb} {$name} \"{$label}\"";
    }
}
