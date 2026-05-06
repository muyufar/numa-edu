<?php

namespace App\Notifications\Channels;

use App\Support\WhatsApp\WhatsAppMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! config('services.whatsapp.enabled')) {
            return;
        }

        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        /** @var WhatsAppMessage|null $payload */
        $payload = $notification->toWhatsApp($notifiable);
        if (! $payload) {
            return;
        }

        $baseUrl = (string) config('services.whatsapp.base_url');
        $token = (string) config('services.whatsapp.token');
        if ($baseUrl === '' || $token === '') {
            Log::info('WhatsApp disabled: missing base_url/token.');

            return;
        }

        try {
            Http::withToken($token)
                ->acceptJson()
                ->post(rtrim($baseUrl, '/').'/send', [
                    'to' => $payload->to,
                    'message' => $payload->message,
                ]);
        } catch (\Throwable $e) {
            Log::warning('WhatsApp send failed: '.$e->getMessage());
        }
    }
}