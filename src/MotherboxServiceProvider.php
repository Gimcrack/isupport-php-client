<?php

namespace Smarch\Motherbox;

use Illuminate\Support\ServiceProvider;

class MotherboxServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // config
        $this->publishes([
            __DIR__.'/Config/motherbox.php' => config_path('motherbox.php')
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge config files
        $this->mergeConfigFrom(__DIR__.'/Config/motherbox.php','motherbox');

        $this->commands(
            Commands\PackageCommand::class,
            Commands\PackageConfigCommand::class /*
            'Smarch\Motherbox\Commands\PackageControllerCommand',
            'Smarch\Motherbox\Commands\PackageModelCommand',
            'Smarch\Motherbox\Commands\PackageMigrationCommand',
            'Smarch\Motherbox\Commands\PackageViewCommand'*/
        );
    }
}
