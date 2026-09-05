<?php

namespace App\Application\Empleados\Commands;

final readonly class UpdateEmpleadoCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombre_completo      = null,
        public ?string $cargo                = null,
        public ?float  $sueldo_mensual       = null,
        public ?string $ci                   = null,
        public ?string $carnet_pdf           = null,
        public ?string $correo               = null,
        public ?string $celular_personal     = null,
        public ?string $celular_corporativo  = null,
        public ?string $direccion            = null,
        public ?string $fecha_ingreso        = null,
        public ?bool   $activo               = null,
    ) {}
}
