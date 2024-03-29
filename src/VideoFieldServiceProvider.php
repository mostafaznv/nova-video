<?php

namespace Mostafaznv\NovaVideo;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\ServiceProvider;


class VideoFieldServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Nova::serving(function(ServingNova $event) {
            Nova::script('nova-video', __DIR__ . '/../dist/field.js');
            Nova::style('nova-video', __DIR__ . '/../dist/field.css');
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/config.php' => config_path('nova-video.php')], 'config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'nova-video');
    }
}
