<?php

namespace App\Application\Cartas\Commands;

final readonly class CreateCartaCommand
{
    public function __construct(
        public int     $id_carta,
        public int     $id_us_reg,
        public int     $num_carta,
        public ?int    $id_us,
        public ?int    $id_plan,
        public ?string $nombresenor,
        public ?string $nombretitulo,
        public ?string $textocarta1,
        public ?string $textocarta2,
        public ?string $textocarta3,
        public int     $estado,
    ) {}
}
