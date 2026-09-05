<?php

namespace App\Application\EnviosCertificado\Handlers;

use App\Application\EnviosCertificado\Commands\CreateEnvioCertificadoCommand;
use App\Application\EnviosCertificado\DTOs\EnvioCertificadoDTO;
use App\Domain\EnviosCertificado\Contracts\EnvioCertificadoRepositoryInterface;

class CreateEnvioCertificadoHandler
{
    public function __construct(private readonly EnvioCertificadoRepositoryInterface $repository) {}

    public function handle(CreateEnvioCertificadoCommand $c): EnvioCertificadoDTO
    {
        $model = $this->repository->create([
            'id_ins'         => $c->id_ins,
            'ciudad_destino' => $c->ciudad_destino,
            'fecha_envio'    => $c->fecha_envio,
            'imagen_guia'    => $c->imagen_guia,
            'aclaraciones'   => $c->aclaraciones,
            'costo'          => $c->costo,
            'id_us_reg'      => $c->id_us_reg,
        ]);

        return EnvioCertificadoDTO::fromModel($model);
    }
}
