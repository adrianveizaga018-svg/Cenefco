<?php

namespace App\Application\Imparticiones\Commands;

final readonly class UpdateImparteCommand
{
    public function __construct(
        public int $id,
        public ?string $periodo,
        public ?string $gestion,
        public ?int $id_us,
        public ?int $id_mat,
        public ?string $paralelo,
        public ?string $cupo,
        public ?string $observacion_imp,
        public ?string $nro_resolucion_hcu,
        public ?int $estado,
    ) {}
}
