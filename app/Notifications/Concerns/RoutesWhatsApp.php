<?php

namespace App\Notifications\Concerns;

trait RoutesWhatsApp
{
    public function routeNotificationForWhatsApp(): ?string
    {
        /** @var string|null $phone */
        $phone = $this->phone ?? null;

        return $phone ?: null;
    }
}