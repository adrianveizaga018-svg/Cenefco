<?php

namespace App\Application\DescuentosPromociones\DTOs;

final readonly class DescuentoPromocionDTO
{
    public function __construct(
        public int     $id,
        public string  $codigo,
        public string  $nombre,
        public ?string $descripcion,
        public string  $tipo_descuento,
        public float   $valor,
        public ?float  $monto_minimo,
        public ?int    $usos_maximos,
        public int     $usos_actuales,
        public int     $usos_por_usuario,
        public ?int    $programa_id,
        public bool    $activo,
        public ?string $fecha_inicio,
        public ?string $fecha_fin,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:               $m->id,
            codigo:           $m->codigo,
            nombre:           $m->nombre,
            descripcion:      $m->descripcion ?? null,
            tipo_descuento:   $m->tipo_descuento,
            valor:            (float) $m->valor,
            monto_minimo:     $m->monto_minimo !== null ? (float) $m->monto_minimo : null,
            usos_maximos:     $m->usos_maximos !== null ? (int) $m->usos_maximos : null,
            usos_actuales:    (int) $m->usos_actuales,
            usos_por_usuario: (int) $m->usos_por_usuario,
            programa_id:      $m->programa_id !== null ? (int) $m->programa_id : null,
            activo:           (bool) $m->activo,
            fecha_inicio:     $m->fecha_inicio?->toIso8601String(),
            fecha_fin:        $m->fecha_fin?->toIso8601String(),
            created_at:       (is_string($m->created_at ?? null) ? $m->created_at : $m->created_at?->toIso8601String()),
            updated_at:       (is_string($m->updated_at ?? null) ? $m->updated_at : $m->updated_at?->toIso8601String()),
        );
    }
}
