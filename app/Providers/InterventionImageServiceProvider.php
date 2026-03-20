<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class InterventionImageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('image', function ($app) {
            return new ImageManager(new Driver());
        });
    }
}