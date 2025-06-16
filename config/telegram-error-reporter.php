<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Token
    |--------------------------------------------------------------------------
    |
    | Your Telegram Bot Token from BotFather
    |
    */
    'bot_token' => env('TELEGRAM_ERROR_BOT_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Telegram Chat ID
    |--------------------------------------------------------------------------
    |
    | The chat ID where error reports will be sent
    |
    */
    'chat_id' => env('TELEGRAM_ERROR_CHAT_ID', ''),
]; 