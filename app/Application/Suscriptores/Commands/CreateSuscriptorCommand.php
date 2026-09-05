<?php

declare(strict_types=1);

namespace App\Application\Suscriptores\Commands;

final readonly class CreateSuscriptorCommand
{
    public function __construct(
        public string $email,
        public bool $activo = true,
        public bool $confirmado = false,
        public ?string $nombre = null,
        public ?string $origen = null,
    ) {}
}
