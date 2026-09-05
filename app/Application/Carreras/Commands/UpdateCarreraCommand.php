<?php

namespace App\Application\Carreras\Commands;

final readonly class UpdateCarreraCommand
{
    public function __construct(
        public int $id,
        public ?string $nombre_carrera,
        public ?int $estado,
    ) {}
}
