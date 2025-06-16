<?php

namespace sindaniel\TelegramErrorReporter;

use GuzzleHttp\Client;
use Throwable;

class TelegramErrorReporter
{
    protected $client;
    protected $botToken;
    protected $chatId;

    public function __construct()
    {
        $this->client = new Client();
        $this->botToken = config('telegram-error-reporter.bot_token');
        $this->chatId = config('telegram-error-reporter.chat_id');
    }

    public function report(Throwable $e)
    {
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

        $message = $this->formatMessage($errorData);

        try {
            $this->sendToTelegram($message);
        } catch (\Exception $e) {
            \Log::error('Failed to send error report to Telegram: ' . $e->getMessage());
        }
    }

    protected function formatMessage(array $errorData): string
    {
        return "🚨 *Error Report*\n\n" .
               "📝 *Message:* {$errorData['message']}\n" .
               "📁 *File:* {$errorData['file']}\n" .
               "📌 *Line:* {$errorData['line']}\n" .
               "🌐 *URL:* {$errorData['url']}\n" .
               "📱 *IP:* {$errorData['ip']}\n" .
               "🔍 *User Agent:* {$errorData['user_agent']}\n" .
               "⏰ *Time:* {$errorData['timestamp']}\n" .
               "🆔 *Session:* {$errorData['session_id']}";
    }

    protected function sendToTelegram(string $message)
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
        
        $this->client->post($url, [
            'form_params' => [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]
        ]);
    }
} 