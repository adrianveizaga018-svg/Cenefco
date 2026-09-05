<?php

namespace App\Application\TiposBanco\Handlers;

use App\Application\TiposBanco\Commands\CreateTipoBancoCommand;
use App\Application\TiposBanco\DTOs\TipoBancoDTO;
use App\Domain\TiposBanco\Contracts\TipoBancoRepositoryInterface;

class CreateTipoBancoHandler
{
    public function __construct(private readonly TipoBancoRepositoryInterface $repository) {}

    public function handle(CreateTipoBancoCommand $c): TipoBancoDTO
    {
        $model = $this->repository->create([
            'nombre' => $c->nombre,
            'activo' => $c->activo,
            'orden'  => $c->orden,
        ]);

        return TipoBancoDTO::fromModel($model);
    }
}
