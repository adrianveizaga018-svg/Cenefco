<?php

namespace App\Application\Expedidos\Commands;

final readonly class CreateExpedidoCommand
{
    public function __construct(
        public string $nombre,
        public int    $orden,
        public bool   $activo,
    ) {}
}
