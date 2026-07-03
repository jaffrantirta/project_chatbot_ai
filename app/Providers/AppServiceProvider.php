<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Observers\ActivityLogObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Models whose create / update / delete actions are recorded
     * in the activity log.
     */
    private const LOGGED_MODELS = [
        \App\Models\Farm::class,
        \App\Models\Chicken::class,
        \App\Models\ChickenType::class,
        \App\Models\HealthRecord::class,
        \App\Models\Disease::class,
        \App\Models\DiseaseCategory::class,
        \App\Models\DiseaseSymptom::class,
        \App\Models\Medicine::class,
        \App\Models\KnowledgeBaseDocument::class,
        \App\Models\SystemPrompt::class,
        \App\Models\ChatSession::class,
        \App\Models\User::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach (self::LOGGED_MODELS as $model) {
            $model::observe(ActivityLogObserver::class);
        }

        Event::listen(Login::class, function (Login $event) {
            ActivityLog::record(
                action: 'login',
                description: "{$event->user->name} masuk ke aplikasi",
                userId: $event->user->id,
            );
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                ActivityLog::record(
                    action: 'logout',
                    description: "{$event->user->name} keluar dari aplikasi",
                    userId: $event->user->id,
                );
            }
        });
    }
}
