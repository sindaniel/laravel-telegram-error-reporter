<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Token
    |--------------------------------------------------------------------------
    |
    | The bot token you received from BotFather when creating your Telegram bot.
    | You can get this by messaging @BotFather on Telegram and creating a new bot.
    |
    */
    'bot_token' => env('TELEGRAM_ERROR_BOT_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Telegram Chat ID
    |--------------------------------------------------------------------------
    |
    | The chat ID where error messages will be sent. This can be:
    | - Your personal chat ID (get it from @userinfobot)
    | - A group chat ID (add your bot to the group and get the ID)
    | - A channel ID (add your bot as admin and get the channel ID)
    |
    */
    'chat_id' => env('TELEGRAM_ERROR_CHAT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable Error Reporting
    |--------------------------------------------------------------------------
    |
    | Set to false to disable Telegram error reporting without removing the code.
    | Useful for local development or when you want to temporarily disable notifications.
    |
    */
    'enabled' => env('TELEGRAM_ERROR_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Environment Filter
    |--------------------------------------------------------------------------
    |
    | Only send error reports in these environments.
    | Leave empty to send in all environments.
    |
    */
    'environments' => ['production'],

    /*
    |--------------------------------------------------------------------------
    | Error Level Threshold
    |--------------------------------------------------------------------------
    |
    | Only report errors of this level or higher.
    | Available levels: emergency, alert, critical, error, warning, notice, info, debug
    |
    */
    'level_threshold' => 'error',

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Prevent spam by limiting the number of error reports per minute.
    | Set to 0 to disable rate limiting.
    |
    */
    'rate_limit_per_minute' => 5,

    /*
    |--------------------------------------------------------------------------
    | Message Template
    |--------------------------------------------------------------------------
    |
    | Customize the error message template. Available variables:
    | {app_name}, {environment}, {message}, {file}, {line}, {url}, {ip}, 
    | {user_agent}, {timestamp}, {session_id}
    |
    */
    'message_template' => "🚨 *Error in {app_name}*\n\n" .
                         "**Environment:** {environment}\n" .
                         "**Message:** {message}\n" .
                         "**File:** {file}:{line}\n" .
                         "**URL:** {url}\n" .
                         "**IP:** {ip}\n" .
                         "**Time:** {timestamp}\n" .
                         "**Session:** {session_id}",
];