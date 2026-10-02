<?php

namespace App\Application\AjustesSueldo\DTOs;

final readonly class AjusteSueldoDTO
{
    public function __construct(
        public int     $id,
        public int     $empleado_id,
        public ?string $empleado_nombre,
        public int     $anio,
        public int     $mes,
        public string  $tipo,
        public float   $monto,
        public string  $motivo,
        public bool    $aplicado,
        public ?int    $planilla_detalle_id,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $m): self
    {

        $empleadoNombre = null;
        if (method_exists($m, 'relationLoaded') && $m->relationLoaded('empleado')) {
            $empleadoNombre = $m->empleado?->nombre_completo;
        }

        return new self(
            id:                   (int) $m->id,
            empleado_id:          (int) $m->empleado_id,
            empleado_nombre:      $empleadoNombre ?? ($m->empleado_nombre ?? null),
            anio:                 (int) $m->anio,
            mes:                  (int) $m->mes,
            tipo:                 $m->tipo,
            monto:                (float) $m->monto,
            motivo:               $m->motivo,
            aplicado:             (bool) $m->aplicado,
            planilla_detalle_id:  $m->planilla_detalle_id !== null ? (int) $m->planilla_detalle_id : null,
            created_at:           $m->created_at ? (string) $m->created_at : null,
        );
    }
}
