<?php

namespace App\Application\Empleados\Commands;

final readonly class CreateEmpleadoCommand
{
    public function __construct(
        public string  $nombre_completo,
        public string  $cargo,
        public float   $sueldo_mensual,
        public string  $ci,
        public string  $carnet_pdf,
        public ?string $correo               = null,
        public ?string $celular_personal     = null,
        public ?string $celular_corporativo  = null,
        public ?string $direccion            = null,
        public ?string $fecha_ingreso        = null,
        public bool    $activo               = true,
    ) {}
}
