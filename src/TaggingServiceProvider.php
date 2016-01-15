<?php

namespace Waavi\Tagging;

use Illuminate\Support\ServiceProvider;

class TaggingServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/tagging.php'   => config_path('tagging.php'),
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ]);
        $this->mergeConfigFrom(
            __DIR__ . '/../config/tagging.php', 'tagging'
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Contracts\TagInterface::class, config('tagging.model'));
    }
}
