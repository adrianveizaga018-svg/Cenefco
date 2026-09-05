<?php

namespace App\Http\Traits;

use App\Infrastructure\Shared\Services\WhatsAppService;

trait WhatsAppServiceTrait
{
    private WhatsAppService $wa;

    private function initializeWhatsAppService(): void
    {
        $this->wa = new WhatsAppService;
    }
}
