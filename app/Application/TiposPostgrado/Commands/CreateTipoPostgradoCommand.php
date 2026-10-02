<?php

namespace App\Application\TiposPostgrado\Commands;

final readonly class CreateTipoPostgradoCommand
{
    public function __construct(
        public int $idTipopost,
        public int $idPlan,
        public int $idUsReg,
        public int $numTipopost,
        public ?int $idTipopago,
        public ?string $descuentopostgrado,
        public ?string $calculoCuota,
    ) {}
}
