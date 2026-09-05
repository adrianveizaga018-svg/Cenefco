<?php

namespace App\Application\Monografias\Commands;

final readonly class CreateMonografiaCommand
{
    public function __construct(
        public int     $id_monografia,
        public int     $id_us_reg,
        public int     $num_monografia,
        public string  $titulo_monografia,
        public ?string $descripcion_monografia,
        public ?string $fecha_publicacion,
        public ?string $autor,
        public ?string $archivo,
        public int     $estado,
    ) {}
}
