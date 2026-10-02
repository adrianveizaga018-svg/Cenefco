<?php

namespace App\Application\Honorarios\Handlers;

use App\Application\Honorarios\Commands\UpsertConfigHonorarioCommand;
use App\Application\Honorarios\DTOs\ConfigHonorarioDTO;
use App\Domain\Honorarios\Contracts\ConfigHonorarioRepositoryInterface;

class UpsertConfigHonorarioHandler
{
    public function __construct(private readonly ConfigHonorarioRepositoryInterface $repository) {}

    public function handle(UpsertConfigHonorarioCommand $c): ConfigHonorarioDTO
    {
        return $this->repository->upsert($c->id_programa, [
            'tipo_honorario' => $c->tipo_honorario,
            'monto_fijo'     => $c->monto_fijo,
            'monto_por_dia'  => $c->monto_por_dia,
        ]);
    }
}
