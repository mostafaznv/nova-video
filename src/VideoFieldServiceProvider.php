<?php

namespace Mostafaznv\NovaVideo;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\ServiceProvider;

class VideoFieldServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Nova::serving(function(ServingNova $event) {
            Nova::script('nova-video', __DIR__ . '/../dist/js/field.js');
        });
    }
}
