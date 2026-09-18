<?php

namespace App\Application\TiposBanco\Commands;

final readonly class CreateTipoBancoCommand
{
    public function __construct(
        public string $nombre,
        public ?string $numero_cuenta = null,
        public ?string $titular = null,
        public bool   $activo = true,
        public int    $orden  = 0,
    ) {}
}
