# Laravel Telegram Error Reporter

Un paquete para Laravel que reporta automáticamente los errores 500 de tu aplicación a Telegram.

## Instalación

Instala el paquete via Composer:

```bash
composer require sindaniel/laravel-telegram-error-reporter
```

Publica el archivo de configuración:

```bash
php artisan vendor:publish --provider="Sindaniel\LaravelTelegramErrorReporter\TelegramErrorReporterServiceProvider" --tag="config"
```

## Configuración

### 1. Crear un Bot de Telegram

1. Abre Telegram y busca `@BotFather`
2. Envía `/newbot` y sigue las instrucciones
3. Guarda el token que te proporciona BotFather

### 2. Obtener el Chat ID

Para obtener tu Chat ID personal:
1. Busca `@userinfobot` en Telegram
2. Envía `/start` y obtendrás tu Chat ID

Para un grupo:
1. Agrega tu bot al grupo
2. Envía un mensaje cualquiera en el grupo
3. Visita: `https://api.telegram.org/bot<TU_BOT_TOKEN>/getUpdates`
4. Busca el `chat.id` en la respuesta

### 3. Variables de Entorno

Agrega estas variables a tu archivo `.env`:

```env
TELEGRAM_ERROR_BOT_TOKEN=tu_bot_token_aqui
TELEGRAM_ERROR_CHAT_ID=tu_chat_id_aqui
TELEGRAM_ERROR_ENABLED=true
```

## Uso

### Configuración Básica en bootstrap/app.php

Reemplaza tu código actual con este:

```php
->withExceptions(function (Exceptions $exceptions) {
    $exceptions->report(function (Throwable $e) {
        // Tu código actual para logging
        $errorData = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'url' => request()->fullUrl(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
            'session_id' => session()->getId(),
        ];
        
        info($errorData);
        
        // Reportar a Telegram
        app(\Sindaniel\LaravelTelegramErrorReporter\TelegramErrorReporter::class)
            ->report($e, $errorData);
    });
});
```

### Uso con Facade

También puedes usar el facade en cualquier parte de tu código:

```php
use Sindaniel\LaravelTelegramErrorReporter\TelegramErrorReporterFacade as TelegramError;

try {
    // Tu código que puede fallar
} catch (Exception $e) {
    TelegramError::report($e, ['additional_context' => 'some value']);
}
```

### Probar la Configuración

Puedes probar si tu configuración funciona correctamente:

```php
use Sindaniel\LaravelTelegramErrorReporter\TelegramErrorReporterFacade as TelegramError;

$result = TelegramError::test();
dd($result);
```

## Configuración Avanzada

El archivo de configuración `config/telegram-error-reporter.php` contiene las siguientes opciones:

### Filtros por Entorno

```php
'environments' => ['production'], // Solo reportar en producción
```

### Limitación de Rate

```php
'rate_limit_per_minute' => 5, // Máximo 5 errores por minuto
```

### Plantilla de Mensaje Personalizada

```php
'message_template' => "🚨 *Error en {app_name}*\n\n" .
                     "**Entorno:** {environment}\n" .
                     "**Mensaje:** {message}\n" .
                     "**Archivo:** {file}:{line}\n" .
                     "**URL:** {url}\n" .
                     "**IP:** {ip}\n" .
                     "**Tiempo:** {timestamp}",
```

Variables disponibles:
- `{app_name}` - Nombre de la aplicación
- `{environment}` - Entorno actual (production, local, etc.)
- `{message}` - Mensaje del error
- `{file}` - Archivo donde ocurrió el error
- `{line}` - Línea donde ocurrió el error
- `{url}` - URL donde ocurrió el error
- `{ip}` - IP del usuario
- `{user_agent}` - User agent del navegador
- `{timestamp}` - Fecha y hora del error
- `{session_id}` - ID de la sesión

## Ejemplo de Artisan Command para Pruebas

Crea un comando de Artisan para probar el reporter:

```bash
php artisan make:command TestTelegramError
```

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Sindaniel\LaravelTelegramErrorReporter\TelegramErrorReporterFacade as TelegramError;

class TestTelegramError extends Command
{
    protected $signature = 'telegram:test-error';
    protected $description = 'Test Telegram error reporting';

    public function handle()
    {
        try {
            // Crear un error de prueba
            throw new \Exception('Este es un error de prueba para Telegram');
        } catch (\Exception $e) {
            $result = TelegramError::report($e, [
                'test_context' => 'Comando de prueba ejecutado',
                'user' => 'Sistema'
            ]);
            
            if ($result) {
                $this->info('✅ Error reportado exitosamente a Telegram');
            } else {
                $this->error('❌ Error al reportar a Telegram');
            }
        }
    }
}
```

## Características

- ✅ Reportes automáticos de errores 500
- ✅ Filtros por entorno (solo producción, staging, etc.)
- ✅ Rate limiting para evitar spam
- ✅ Plantillas de mensaje personalizables
- ✅ Soporte para contexto adicional
- ✅ Manejo seguro de errores (no causa errores adicionales)
- ✅ Escape automático de caracteres especiales de Markdown
- ✅ Timeout configurables para las peticiones HTTP

## Licencia

MIT License

## Contribuir

Las contribuciones son bienvenidas. Por favor, abre un issue o pull request.

## Changelog

### 1.0.0
- Versión inicial
- Reporte básico de errores a Telegram
- Configuración via archivo de config y variables de entorno