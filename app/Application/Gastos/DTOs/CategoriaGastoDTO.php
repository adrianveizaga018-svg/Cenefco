<?php

namespace App\Application\Gastos\DTOs;

final readonly class CategoriaGastoDTO
{
    public function __construct(
        public int     $id,
        public string  $nombre,
        public ?string $linea_negocio,
        public bool    $activo,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:            (int) $m->id,
            nombre:        $m->nombre,
            linea_negocio: $m->linea_negocio ?? null,
            activo:        (bool) $m->activo,
        );
    }
}
