<?php

namespace Sindaniel\LaravelTelegramErrorReporter;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramErrorReporter
{
    protected $botToken;
    protected $chatId;
    protected $client;

    public function __construct(?string $botToken = null, ?string $chatId = null)
    {
        $this->botToken = config('telegram-error-reporter.bot_token');
        $this->chatId = config('telegram-error-reporter.chat_id');
        $this->client = new Client([
            'timeout' => 10,
            'connect_timeout' => 5,
        ]);

        
   
    }

    /**
     * Report an error to Telegram
     */
    public function report(Throwable $exception, array $context = []): bool
    {
        try {

            
            // Check if reporting is enabled
            if (!config('telegram-error-reporter.enabled', true)) {
                return false;
            }

            
            // Check environment filter
          
            $allowedEnvironments = config('telegram-error-reporter.environments', []);
          
            if (!empty($allowedEnvironments) && !in_array(app()->environment(), $allowedEnvironments)) {
                return false;
            }

            // Check if we have required config
        
            if (empty($this->botToken) || empty($this->chatId)) {
                Log::warning('Telegram Error Reporter: Missing bot_token or chat_id configuration');
                return false;
            }

            // Rate limiting
            
            if (!$this->checkRateLimit()) {
                return false;
            }


            // Prepare error data
            $errorData = $this->prepareErrorData($exception, $context);

            // Format message
            $message = $this->formatMessage($errorData);
          
            // Send to Telegram
            return $this->sendToTelegram($message);

        } catch (Throwable $e) {
            // Don't let the error reporter cause more errors
            Log::error('Telegram Error Reporter failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Prepare error data array
     */
    protected function prepareErrorData(Throwable $exception, array $context = []): array
    {
        
        return array_merge([
            'app_name' => config('app.name', 'Laravel App'),
            'environment' => app()->environment(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'url' => request()->fullUrl(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
            'session_id' => session()->getId(),
            'user_id' => request()->user()->id ?? 'oe',
        ], $context);
    }

    /**
     * Format the error message using template
     */
    protected function formatMessage(array $errorData): string
    {
        $template = config('telegram-error-reporter.message_template', 
            "🚨 *Error in {app_name}*\n\n**Message:** {message}\n**File:** {file}:{line}"
        );

        foreach ($errorData as $key => $value) {
            $template = str_replace('{' . $key . '}', $this->escapeMarkdown($value), $template);
        }

        return $template;
    }

    /**
     * Escape special characters for Telegram Markdown
     */
    protected function escapeMarkdown(?string $text): string
    {
        if ($text === null) {
            return '';
        }

        $text = str_replace(['*', '_', '`', '['], ['\*', '\_', '\`', '\['], $text);
        return mb_substr($text, 0, 1000); // Limit length
    }

    /**
     * Check rate limiting
     */
    protected function checkRateLimit(): bool
    {
        $rateLimit = config('telegram-error-reporter.rate_limit_per_minute', 5);
        
        if ($rateLimit <= 0) {
            return true;
        }

        $cacheKey = 'telegram_error_reporter_rate_limit';
        $currentCount = Cache::get($cacheKey, 0);

        if ($currentCount >= $rateLimit) {
            return false;
        }

        Cache::put($cacheKey, $currentCount + 1, 60); // 60 seconds
        return true;
    }

    /**
     * Send message to Telegram
     */
    protected function sendToTelegram(string $message): bool
    {
        try {
            $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

            $response = $this->client->post($url, [
                'json' => [
                    'chat_id' => $this->chatId,
                    'text' => $message,
                    'parse_mode' => 'Markdown',
                    'disable_web_page_preview' => true,
                ],
            ]);

            return $response->getStatusCode() === 200;

        } catch (RequestException $e) {
            Log::error('Failed to send Telegram message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Test the Telegram connection
     */
    public function test(): array
    {
        try {
            $url = "https://api.telegram.org/bot{$this->botToken}/getMe";
            $response = $this->client->get($url);
            $data = json_decode($response->getBody(), true);

            if ($data['ok'] ?? false) {
                // Send test message
                $testMessage = "🧪 *Test Message*\n\nTelegram Error Reporter is working correctly!";
                $sent = $this->sendToTelegram($testMessage);

                return [
                    'success' => $sent,
                    'bot_info' => $data['result'] ?? null,
                    'message' => $sent ? 'Test message sent successfully!' : 'Failed to send test message',
                ];
            }

            return [
                'success' => false,
                'message' => 'Invalid bot token or bot configuration error',
                'error' => $data['description'] ?? 'Unknown error',
            ];

        } catch (RequestException $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
            ];
        }
    }
}