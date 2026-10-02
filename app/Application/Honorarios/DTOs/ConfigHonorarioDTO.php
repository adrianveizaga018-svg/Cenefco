<?php

namespace App\Application\Honorarios\DTOs;

final readonly class ConfigHonorarioDTO
{
    public function __construct(
        public int     $id,
        public int     $id_programa,
        public ?string $nombre_programa,
        public string  $tipo_honorario,
        public ?float  $monto_fijo,
        public ?float  $monto_por_dia,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:              (int) $m->id,
            id_programa:     (int) $m->id_programa,
            nombre_programa: $m->nombre_programa ?? null,
            tipo_honorario:  $m->tipo_honorario,
            monto_fijo:      $m->monto_fijo !== null ? (float) $m->monto_fijo : null,
            monto_por_dia:   $m->monto_por_dia !== null ? (float) $m->monto_por_dia : null,
        );
    }
}
