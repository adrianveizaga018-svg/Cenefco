<?php

namespace App\Domain\BannersPortal\Exceptions;

use RuntimeException;

class BannerPortalNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Banner del portal '{$id}' no encontrado.", 404);
    }
}
