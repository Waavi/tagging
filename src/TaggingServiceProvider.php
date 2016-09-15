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
        $this->publishes([__DIR__ . '/../config/tagging.php' => config_path('tagging.php')]);
        $this->publishes([__DIR__ . '/../database/migrations/' => database_path('migrations')], 'migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/tagging.php', 'tagging');
        $translatable = config('tagging.translatable');
        if ($translatable) {
            $this->app->bind(Contracts\TagInterface::class, Models\TranslatableTag::class);
        } else {
            $this->app->bind(Contracts\TagInterface::class, Models\Tag::class);
        }
    }
}
