<?php

namespace KusikusiCMS\Models;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Console\AboutCommand;
use KusikusiCMS\Models\Listeners\EntityEventSubscriber;

class ModelsServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/kusikusicms-models.php', 'kusikusicms-models');
        $this->app->register(EntityEventsServiceProvider::class);
    }
    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        AboutCommand::add('KusikusiCMS core models package', fn () => ['Version' => '10.0.0-alpha.1']);
        $this->publishes([__DIR__.'/../config/kusikusicms-models.php' => config_path('kusikusicms-models.php')]);
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        EntityEventSubscriber::class,
    ];
}
