<?php

declare(strict_types=1);

namespace App\Application\Suscriptores\Commands;

final readonly class UpdateSuscriptorCommand
{
    public function __construct(
        public int|string $id,
        public ?string $nombre = null,
        public ?bool $confirmado = null,
        public ?bool $activo = null,
    ) {}
}
