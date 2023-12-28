<?php

namespace KusikusiCMS\Models;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Console\AboutCommand;
use KusikusiCMS\Models\Observers\EntityObserver;

class ModelsServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/kusikusicms-models.php', 'kusikusicms-models'
        );
    }
    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/kusikusicms-models.php' => config_path('kusikusicms-models.php'),
        ]);
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        AboutCommand::add('KusikusiCMS core models package', fn () => ['Version' => '10.0.0-alpha.1']);
        Entity::observe(EntityObserver::class);
    }
}
