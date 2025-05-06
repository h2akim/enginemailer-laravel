<?php

namespace HakimRazalan\EngineMailerDriver;

use HakimRazalan\EngineMailerDriver\Client\EngineMailer;
use HakimRazalan\EngineMailerDriver\Transport\EngineMailerTransport;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use HakimRazalan\EngineMailer\Client;
use Illuminate\Support\ServiceProvider;

class EngineMailerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Mail::extend('enginemailer', function (array $config) {
            $config = array_merge($this->app['config']->get('enginemailer', []), $config);

            $engineMailer = Client::setup(Arr::get($config, 'api_key'));

            return new EngineMailerTransport($engineMailer);
        });      
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/enginemailer.php', 'enginemailer');
    }
}