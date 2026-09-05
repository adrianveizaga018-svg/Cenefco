<?php

namespace App\Application\Gastos\Commands;

final readonly class CreateCategoriaGastoCommand
{
    public function __construct(
        public string  $nombre,
        public ?string $linea_negocio = null,
        public bool    $activo        = true,
    ) {}
}
