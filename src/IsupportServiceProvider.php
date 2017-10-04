<?php

namespace Ingenious\Isupport;

use Ingenious\Isupport\Isupport;
use Illuminate\Support\Collection;
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

        Collection::macro('stddev', function() {
            $the_mean = $this->avg();

            if ( $this->count() - 1 == 0 ) return 0;

            return sqrt($this->reduce( function($result, $item) use ($the_mean) {
                return $result + pow($item - $the_mean, 2);
            }, 0) / ( $this->count() - 1 ));
        });

        /**
         * Remove outliers from dataset
         */
        Collection::macro('remove_outliers', function($magnitude = 3) {
            if ( ! $this->count() ) return $this;
            $mean = $this->avg();
            $stddev = $this->stddev();
            $outlier = $magnitude * $stddev;

            return $this->filter(function( $value ) use ($mean, $outlier) {
                return $mean-$outlier <= $value && $value <= $mean+$outlier;
            })->values();
        });
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
