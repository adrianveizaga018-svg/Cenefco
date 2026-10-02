<?php

namespace App\Application\Empleados\DTOs;

final readonly class EmpleadoDTO
{
    public function __construct(
        public int     $id,
        public string  $nombre_completo,
        public string  $cargo,
        public float   $sueldo_mensual,
        public string  $ci,
        public ?string $carnet_pdf,
        public ?string $correo,
        public ?string $celular_personal,
        public ?string $celular_corporativo,
        public ?string $direccion,
        public ?string $fecha_ingreso,
        public bool    $activo,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:                   (int) $m->id,
            nombre_completo:      $m->nombre_completo,
            cargo:                $m->cargo,
            sueldo_mensual:       (float) $m->sueldo_mensual,
            ci:                   $m->ci,
            carnet_pdf:           $m->carnet_pdf ?? null,
            correo:               $m->correo ?? null,
            celular_personal:     $m->celular_personal ?? null,
            celular_corporativo:  $m->celular_corporativo ?? null,
            direccion:            $m->direccion ?? null,
            fecha_ingreso:        $m->fecha_ingreso ? (string) $m->fecha_ingreso : null,
            activo:               (bool) $m->activo,
        );
    }
}
