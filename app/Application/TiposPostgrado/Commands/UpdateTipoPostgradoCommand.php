<?php

namespace App\Application\TiposPostgrado\Commands;

final readonly class UpdateTipoPostgradoCommand
{
    public function __construct(
        public int $idTipopost,
        public ?int $idTipopago,
        public ?string $descuentopostgrado,
        public ?string $calculoCuota,
        public ?int $estado,
    ) {}
}
