<?php

namespace Sindaniel\LaravelTelegramErrorReporter;

use Illuminate\Support\ServiceProvider;

class TelegramErrorReporterServiceProvider extends ServiceProvider
{
    public function register()
    {
        
        $this->mergeConfigFrom(
            __DIR__.'/../config/telegram-error-reporter.php',
            'telegram-error-reporter'
        );

        $this->app->singleton(TelegramErrorReporter::class, function ($app) {
       
            return new TelegramErrorReporter(
                config('telegram-error-reporter.bot_token'),
                config('telegram-error-reporter.chat_id')
            );
        });
    }

    public function boot()
    {
        
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/telegram-error-reporter.php' => config_path('telegram-error-reporter.php'),
            ], 'config');
        }
    }
}