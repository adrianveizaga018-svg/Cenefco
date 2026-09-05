<?php

namespace App\Application\Planillas\DTOs;

final readonly class PlanillaDetalleDTO
{
    public function __construct(
        public int    $id,
        public int    $empleado_id,
        public string $nombre_completo,
        public string $cargo,
        public float  $monto,
        public ?float $monto_base,
        public float  $total_descuentos,
        public float  $total_bonos,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:               (int) $m->id,
            empleado_id:      (int) $m->empleado_id,
            nombre_completo:  $m->nombre_completo,
            cargo:            $m->cargo,
            monto:            (float) $m->monto,
            monto_base:       $m->monto_base !== null ? (float) $m->monto_base : null,
            total_descuentos: (float) ($m->total_descuentos ?? 0),
            total_bonos:      (float) ($m->total_bonos ?? 0),
        );
    }
}
