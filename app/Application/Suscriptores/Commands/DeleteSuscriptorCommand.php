<?php

declare(strict_types=1);

namespace App\Application\Suscriptores\Commands;

final readonly class DeleteSuscriptorCommand
{
    public function __construct(
        public int|string $id,
    ) {}
}
