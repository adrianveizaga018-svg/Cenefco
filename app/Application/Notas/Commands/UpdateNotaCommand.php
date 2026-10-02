<?php

namespace App\Application\Notas\Commands;

final readonly class UpdateNotaCommand
{
    public function __construct(
        public int $id,
        public ?int $nota,
        public ?int $nota_seg,
        public ?string $paralelo,
        public ?int $mostrarcert_notas,
        public ?int $estado,
    ) {}
}
