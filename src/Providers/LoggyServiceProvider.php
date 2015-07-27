<?php namespace Hewerthomn\Loggy\Providers;

use Illuminate\Support\ServiceProvider;

class LoggyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->handleConfigs();
        $this->handleMigrations();

        $this->handleRoutes();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    private function handleConfigs()
    {
        $configPath = __DIR__.'/../../config/loggy.php';

        $this->publishes([$configPath => config_path('loggy.php')]);
        $this->mergeConfigFrom($configPath, 'loggy');
    }

    private function handleMigrations()
    {
        $this->publishes([__DIR__.'/../../migrations' => base_path('database/migrations')]);
    }

    private function handleRoutes()
    {
        include __DIR__.'/../../routes.php';
    }
}
