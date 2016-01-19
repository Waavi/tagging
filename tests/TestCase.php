<?php

namespace Waavi\Tagging\Test;

use Orchestra\Testbench\TestCase as Orchestra;
use Waavi\Translation\Repositories\LanguageRepository;

abstract class TestCase extends Orchestra
{
    public function setUp($withDifferentModels = false)
    {
        parent::setUp();
        if ($withDifferentModels) {
            $this->app['config']->set('tagging.uses_tags_for_different_models', true);
        }
        $this->setUpDatabase($this->app);
    }
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Waavi\Tagging\TaggingServiceProvider::class,
            \Waavi\Translation\TranslationServiceProvider::class,
            \Cviebrock\EloquentSluggable\SluggableServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $this->artisan('migrate', ['--realpath' => realpath(__DIR__ . '/../database/migrations')]);
        $this->artisan('migrate', ['--realpath' => realpath(__DIR__ . '/../vendor/waavi/translation/database/migrations')]);
        // Seed the spanish and english languages
        $languageRepository = \App::make(LanguageRepository::class);
        $languageRepository->create(['locale' => 'en', 'name' => 'English']);
        $languageRepository->create(['locale' => 'es', 'name' => 'Spanish']);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('app.key', 'sF5r4kJy5HEcOEx3NWxUcYj1zLZLHxuu');
    }
}
