<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use ReCaptcha\ReCaptcha;

class RecaptchaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ReCaptcha::class, function ($app) {
            return new ReCaptcha(config('services.recaptcha.secret_key'));
        });
    }
}