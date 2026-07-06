<?php

namespace App\Support\WhatsApp;

class ManualWhatsAppLink
{
    public static function url(?string $phone, string $message): ?string
    {
        $normalized = PhoneNumber::normalize($phone);
        if (! $normalized || trim($message) === '') {
            return null;
        }

        return 'https://wa.me/'.$normalized.'?text='.rawurlencode($message);
    }
}
