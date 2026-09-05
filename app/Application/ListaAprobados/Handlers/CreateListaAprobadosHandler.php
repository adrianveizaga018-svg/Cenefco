<?php

namespace App\Application\ListaAprobados\Handlers;

use App\Application\ListaAprobados\Commands\CreateListaAprobadosCommand;
use App\Application\ListaAprobados\DTOs\ListaAprobadosDTO;
use App\Domain\ListaAprobados\Contracts\ListaAprobadosRepositoryInterface;

class CreateListaAprobadosHandler
{
    public function __construct(
        private readonly ListaAprobadosRepositoryInterface $repository
    ) {}

    public function handle(CreateListaAprobadosCommand $command): ListaAprobadosDTO
    {
        $row = $this->repository->create([
            'imparte_id'         => $command->imparte_id,
            'usuario_id'         => $command->usuario_id,
            'inscripcion_id'     => $command->inscripcion_id,
            'nota_final'         => $command->nota_final,
            'nota_minima'        => $command->nota_minima,
            'condicion'          => $command->condicion,
            'observacion'        => $command->observacion,
            'comprobante_url'    => $command->comprobante_url,
            'ajuste_manual'      => $command->ajuste_manual,
            'estado_certificado' => $command->estado_certificado,
            'notificado_email'   => false,
            'registrado_por'     => $command->registrado_por,
            'id_us_reg'          => $command->id_us_reg,
            'created_at'         => now(),
        ]);

        return ListaAprobadosDTO::fromRow($row);
    }
}
