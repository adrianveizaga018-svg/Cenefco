<?php

namespace App\Application\Notas\Commands;

final readonly class CreateNotaCommand
{
    public function __construct(
        public int $id_not,
        public ?int $id_us_reg,
        public ?string $periodo,
        public ?string $gestion,
        public int $id_imp,
        public int $id_us,
        public ?int $id_mat,
        public int $nota,
        public int $nota_seg,
        public ?string $paralelo,
        public int $estado,
    ) {}
}
