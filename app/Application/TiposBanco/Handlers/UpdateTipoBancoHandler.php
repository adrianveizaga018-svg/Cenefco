<?php

namespace App\Application\TiposBanco\Handlers;

use App\Application\TiposBanco\Commands\UpdateTipoBancoCommand;
use App\Application\TiposBanco\DTOs\TipoBancoDTO;
use App\Domain\TiposBanco\Contracts\TipoBancoRepositoryInterface;

class UpdateTipoBancoHandler
{
    public function __construct(private readonly TipoBancoRepositoryInterface $repository) {}

    public function handle(UpdateTipoBancoCommand $c): TipoBancoDTO
    {
        $data = array_filter([
            'nombre' => $c->nombre,
            'activo' => $c->activo,
            'orden'  => $c->orden,
        ], fn ($v) => $v !== null);

        $model = $this->repository->update($c->id, $data);

        return TipoBancoDTO::fromModel($model);
    }
}
