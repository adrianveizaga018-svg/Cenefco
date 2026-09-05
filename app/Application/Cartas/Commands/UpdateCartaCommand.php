<?php

namespace App\Application\Cartas\Commands;

final readonly class UpdateCartaCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombresenor,
        public ?string $nombretitulo,
        public ?string $textocarta1,
        public ?string $textocarta2,
        public ?string $textocarta3,
        public ?int    $estado,
    ) {}
}
