<?php

namespace App\Application\TiposBanco\Commands;

final readonly class UpdateTipoBancoCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombre = null,
        public ?bool   $activo = null,
        public ?int    $orden  = null,
    ) {}
}
