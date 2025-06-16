# Laravel Telegram Error Reporter

A Laravel package that automatically reports errors to Telegram using a bot.

## Installation

1. Install the package via Composer:

```bash
composer require daniel/laravel-telegram-error-reporter
```

2. Publish the configuration file:

```bash
php artisan vendor:publish --provider="Daniel\TelegramErrorReporter\TelegramErrorReporterServiceProvider" --tag="config"
```

3. Add the following environment variables to your `.env` file:

```
TELEGRAM_ERROR_BOT_TOKEN=your_bot_token_here
TELEGRAM_ERROR_CHAT_ID=your_chat_id_here
```

## Configuration

1. Create a new Telegram bot using [@BotFather](https://t.me/botfather) and get your bot token
2. Get your chat ID by:
   - Adding your bot to a group
   - Sending a message in the group
   - Accessing: `https://api.telegram.org/bot<YourBOTToken>/getUpdates`
   - Look for the "chat" object and copy the "id" value

## Usage

The package will automatically report all errors to your configured Telegram chat. No additional code is required.

## Features

- Reports error messages, file, line number
- Includes request URL, IP address, and user agent
- Timestamps and session IDs for tracking
- Formatted messages with emojis for better readability
- Markdown support for better formatting

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information. 