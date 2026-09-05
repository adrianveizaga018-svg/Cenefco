<?php

namespace App\Application\Empleados\Handlers;

use App\Application\Empleados\Commands\UpdateEmpleadoCommand;
use App\Application\Empleados\DTOs\EmpleadoDTO;
use App\Domain\Empleados\Contracts\EmpleadoRepositoryInterface;

class UpdateEmpleadoHandler
{
    public function __construct(private readonly EmpleadoRepositoryInterface $repository) {}

    public function handle(UpdateEmpleadoCommand $c): EmpleadoDTO
    {
        $data = array_filter([
            'nombre_completo'      => $c->nombre_completo,
            'cargo'                => $c->cargo,
            'sueldo_mensual'       => $c->sueldo_mensual,
            'ci'                   => $c->ci,
            'carnet_pdf'           => $c->carnet_pdf,
            'correo'               => $c->correo,
            'celular_personal'     => $c->celular_personal,
            'celular_corporativo'  => $c->celular_corporativo,
            'direccion'            => $c->direccion,
            'fecha_ingreso'        => $c->fecha_ingreso,
        ], fn ($v) => $v !== null);

        if ($c->activo !== null) {
            $data['activo'] = $c->activo;
        }

        return $this->repository->update($c->id, $data);
    }
}
