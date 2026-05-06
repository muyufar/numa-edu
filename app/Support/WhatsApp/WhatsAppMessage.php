<?php

namespace App\Support\WhatsApp;

class WhatsAppMessage
{
    public function __construct(
        public readonly string $to,
        public readonly string $message,
    ) {
    }
}
