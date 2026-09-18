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
        $data = [];
        if ($c->nombre !== null) $data['nombre'] = $c->nombre;
        if ($c->numero_cuenta !== null) $data['numero_cuenta'] = $c->numero_cuenta;
        if ($c->titular !== null) $data['titular'] = $c->titular;
        if ($c->activo !== null) $data['activo'] = $c->activo;
        if ($c->orden !== null) $data['orden'] = $c->orden;

        $model = $this->repository->update($c->id, $data);

        return TipoBancoDTO::fromModel($model);
    }
}
