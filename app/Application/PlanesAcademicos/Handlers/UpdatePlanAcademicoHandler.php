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
        $data = [];

        if ($command->titulo            !== null) $data['titulo']            = $command->titulo;
        if ($command->titulo_plan       !== null) $data['titulo_plan']       = $command->titulo_plan;
        if ($command->convenio          !== null) $data['convenio']          = $command->convenio;
        if ($command->convenio_id       !== null) $data['convenio_id']       = $command->convenio_id;
        if ($command->anio              !== null) $data['anio']              = $command->anio;
        if ($command->numero_resolucion !== null) $data['numero_resolucion'] = $command->numero_resolucion;
        if ($command->costo             !== null) $data['costo']             = $command->costo;
        if ($command->nro_cuotas        !== null) $data['nro_cuotas']        = $command->nro_cuotas;
        if ($command->descuento         !== null) $data['descuento']         = $command->descuento;
        if ($command->costo_por_cuota   !== null) $data['costo_por_cuota']   = $command->costo_por_cuota;
        if ($command->id_catplan        !== null) $data['id_catplan']        = $command->id_catplan;
        if ($command->estado            !== null) $data['estado']            = $command->estado;

        // Handle QR separately because it can be explicitly set to null (remove)
        if ($command->qr_image_url !== null) {
            $data['qr_image_url'] = $command->qr_image_url === 'REMOVE' ? null : $command->qr_image_url;
        }

        return $this->repository->update($command->id, $data);
    }
}
