<?php

namespace App\Application\CompromisosCobro\DTOs;

final readonly class CompromisoCobroLogDTO
{
    public function __construct(
        public int     $id,
        public int     $compromiso_cobro_id,
        public string  $tipo_evento,
        public ?string $fecha_anterior,
        public ?string $fecha_nueva,
        public ?string $motivo,
        public ?string $observacion,
        public int     $registrado_por,
        public ?string $registrado_por_nombre,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:                     (int) $m->id,
            compromiso_cobro_id:    (int) $m->compromiso_cobro_id,
            tipo_evento:            (string) $m->tipo_evento,
            fecha_anterior:         $m->fecha_anterior ?? null,
            fecha_nueva:            $m->fecha_nueva ?? null,
            motivo:                 $m->motivo ?? null,
            observacion:            $m->observacion ?? null,
            registrado_por:         (int) $m->registrado_por,
            registrado_por_nombre:  $m->registrado_por_nombre ?? null,
            created_at:             $m->created_at ? (string) $m->created_at : null,
        );
    }
}
