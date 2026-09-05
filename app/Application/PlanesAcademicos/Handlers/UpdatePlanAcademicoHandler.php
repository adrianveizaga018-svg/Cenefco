<?php

namespace App\Application\PlanesAcademicos\Handlers;

use App\Application\PlanesAcademicos\Commands\UpdatePlanAcademicoCommand;
use App\Application\PlanesAcademicos\DTOs\PlanAcademicoDTO;
use App\Domain\PlanesAcademicos\Contracts\PlanAcademicoRepositoryInterface;

class UpdatePlanAcademicoHandler
{
    public function __construct(
        private readonly PlanAcademicoRepositoryInterface $repository
    ) {}

    public function handle(UpdatePlanAcademicoCommand $command): PlanAcademicoDTO
    {
        $data = array_filter([
            'titulo'            => $command->titulo,
            'titulo_plan'       => $command->titulo_plan,
            'convenio'          => $command->convenio,
            'convenio_id'       => $command->convenio_id,
            'anio'              => $command->anio,
            'numero_resolucion' => $command->numero_resolucion,
            'costo'             => $command->costo,
            'nro_cuotas'        => $command->nro_cuotas,
            'descuento'         => $command->descuento,
            'costo_por_cuota'   => $command->costo_por_cuota,
            'id_catplan'        => $command->id_catplan,
            'estado'            => $command->estado,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
