<?php

namespace sindaniel\TelegramErrorReporter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;
use sindaniel\TelegramErrorReporter\TelegramErrorReporter;

class TelegramErrorReporterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/telegram-error-reporter.php', 'telegram-error-reporter'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/telegram-error-reporter.php' => config_path('telegram-error-reporter.php'),
        ], 'config');

        $this->app->make(ExceptionHandler::class)->reportable(function (\Throwable $e) {
            if (app()->bound(TelegramErrorReporter::class)) {
                app(TelegramErrorReporter::class)->report($e);
            }
        });
    }
} 