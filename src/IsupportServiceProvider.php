<?php

namespace Ingenious\Isupport;

use Ingenious\Isupport\Isupport;
use Illuminate\Support\ServiceProvider;

class IsupportServiceProvider extends ServiceProvider
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
            __DIR__.'/config/isupport.php' => config_path('isupport.php')
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Isupport', function() {
            return new Isupport;
        } );
    }
}
