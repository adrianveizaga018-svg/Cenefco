<?php

namespace App\Application\CampanasPublicidad\Handlers;

use App\Application\CampanasPublicidad\Commands\UpdateCampanaPublicidadCommand;
use App\Application\CampanasPublicidad\DTOs\CampanaPublicidadDTO;
use App\Domain\CampanasPublicidad\Contracts\CampanaPublicidadRepositoryInterface;

class UpdateCampanaPublicidadHandler
{
    public function __construct(private readonly CampanaPublicidadRepositoryInterface $repository) {}

    public function handle(UpdateCampanaPublicidadCommand $c): CampanaPublicidadDTO
    {
        $data = array_filter([
            'programa_id'              => $c->programa_id,
            'proposito'                => $c->proposito,
            'nombre'                   => $c->nombre,
            'plataforma'               => $c->plataforma,
            'objetivo'                 => $c->objetivo,
            'fecha_inicio'             => $c->fecha_inicio,
            'fecha_fin'                => $c->fecha_fin,
            'estado'                   => $c->estado,
            'leads'                    => $c->leads,
            'presupuesto_usd'          => $c->presupuesto_usd,
            'presupuesto_bob'          => $c->presupuesto_bob,
            'id_campana_externa'       => $c->id_campana_externa,
            'responsable'              => $c->responsable,
            'notas'                    => $c->notas,
        ], fn ($v) => $v !== null);

        return $this->repository->update($c->id, $data);
    }
}
