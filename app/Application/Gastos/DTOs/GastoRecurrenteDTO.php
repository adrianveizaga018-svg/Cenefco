<?php

namespace App\Application\Gastos\DTOs;

final readonly class GastoRecurrenteDTO
{
    public function __construct(
        public int     $id,
        public int     $categoria_gasto_id,
        public ?string $categoria_nombre,
        public string  $concepto,
        public float   $monto,
        public int     $dia_del_mes,
        public bool    $activo,
        public ?string $ultima_confirmacion,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:                  (int) $m->id,
            categoria_gasto_id:  (int) $m->categoria_gasto_id,
            categoria_nombre:    $m->categoria->nombre ?? null,
            concepto:            $m->concepto,
            monto:               (float) $m->monto,
            dia_del_mes:         (int) $m->dia_del_mes,
            activo:              (bool) $m->activo,
            ultima_confirmacion: $m->ultima_confirmacion ? (string) $m->ultima_confirmacion : null,
        );
    }
}
