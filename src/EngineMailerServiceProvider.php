<?php

namespace HakimRazalan\EngineMailerLaravel;

use HakimRazalan\EngineMailerLaravel\Transport\EngineMailerTransport;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use HakimRazalan\EngineMailer\Client;
use Illuminate\Support\ServiceProvider;

class EngineMailerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Mail::extend('enginemailer', function (array $config): \HakimRazalan\EngineMailerLaravel\Transport\EngineMailerTransport {
            $config = array_merge($this->app['config']->get('enginemailer', []), $config);

            $engineMailer = Client::setup(Arr::get($config, 'api_key'));

            return new EngineMailerTransport($engineMailer);
        });      
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/enginemailer.php', 'enginemailer');
    }
}