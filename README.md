# Laravel Telegram Error Reporter

A Laravel package that automatically reports your application's 500 errors to Telegram.

## Installation

Install the package via Composer:

```bash
composer require sindaniel/laravel-telegram-error-reporter
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Sindaniel\LaravelTelegramErrorReporter\TelegramErrorReporterServiceProvider" --tag="config"
```

## Configuration

### 1. Create a Telegram Bot

1. Open Telegram and search for `@BotFather`
2. Send `/newbot` and follow the instructions
3. Save the token provided by BotFather

### 2. Get the Chat ID

To get your personal Chat ID:
1. Search for `@userinfobot` in Telegram
2. Send `/start` and you'll get your Chat ID

For a group:
1. Add your bot to the group
2. Send any message in the group
3. Visit: `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates`
4. Look for the `chat.id` in the response

### 3. Environment Variables

Add these variables to your `.env` file:

```env
TELEGRAM_ERROR_BOT_TOKEN=your_bot_token_here
TELEGRAM_ERROR_CHAT_ID=your_chat_id_here
TELEGRAM_ERROR_ENABLED=true
```

## Usage

### Basic Configuration in bootstrap/app.php

Replace your current code with this:

```php
->withExceptions(function (Exceptions $exceptions) {
    $exceptions->report(function (Throwable $e) {
        // Report to Telegram
        app(\Sindaniel\LaravelTelegramErrorReporter\TelegramErrorReporter::class)
            ->report($e, $errorData);
    });
});
```

### Test the Configuration

You can test if your configuration works correctly:

```php
use Sindaniel\LaravelTelegramErrorReporter\TelegramErrorReporterFacade as TelegramError;

$result = TelegramError::test();
dd($result);
```

## Advanced Configuration

The configuration file `config/telegram-error-reporter.php` contains the following options:

### Environment Filters

```php
'environments' => ['production'], // Only report in production
```

### Rate Limiting

```php
'rate_limit_per_minute' => 5, // Maximum 5 errors per minute
```

### Custom Message Template

```php
'message_template' => "🚨 *Error in {app_name}*\n\n" .
                     "**Environment:** {environment}\n" .
                     "**Message:** {message}\n" .
                     "**File:** {file}:{line}\n" .
                     "**URL:** {url}\n" .
                     "**IP:** {ip}\n" .
                     "**Time:** {timestamp}",
```

Available variables:
- `{app_name}` - Application name
- `{environment}` - Current environment (production, local, etc.)
- `{message}` - Error message
- `{file}` - File where the error occurred
- `{line}` - Line where the error occurred
- `{url}` - URL where the error occurred
- `{ip}` - User's IP address
- `{user_agent}` - Browser's user agent
- `{timestamp}` - Date and time of the error
- `{session_id}` - Session ID

## Example Artisan Command for Testing

Create an Artisan command to test the reporter:

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
            // Create a test error
            throw new \Exception('This is a test error for Telegram');
        } catch (\Exception $e) {
            $result = TelegramError::report($e, [
                'test_context' => 'Test command executed',
                'user' => 'System'
            ]);
            
            if ($result) {
                $this->info('✅ Error successfully reported to Telegram');
            } else {
                $this->error('❌ Error reporting to Telegram failed');
            }
        }
    }
}
```

## Features

- ✅ Automatic 500 error reporting
- ✅ Environment filters (production only, staging, etc.)
- ✅ Rate limiting to prevent spam
- ✅ Customizable message templates
- ✅ Additional context support
- ✅ Safe error handling (doesn't cause additional errors)
- ✅ Automatic escaping of Markdown special characters
- ✅ Configurable timeouts for HTTP requests

## License

MIT License

## Contributing

Contributions are welcome. Please open an issue or pull request.

## Changelog

### 1.0.0
- Initial release
- Basic error reporting to Telegram
- Configuration via config file and environment variables