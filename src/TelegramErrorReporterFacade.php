<?php

namespace Sindaniel\LaravelTelegramErrorReporter;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool report(\Throwable $exception, array $context = [])
 * @method static array test()
 */
class TelegramErrorReporterFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TelegramErrorReporter::class;
    }
}