<?php

namespace App\Application\Expedidos\Commands;

final readonly class UpdateExpedidoCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombre,
        public ?int    $orden,
        public ?bool   $activo,
    ) {}
}
