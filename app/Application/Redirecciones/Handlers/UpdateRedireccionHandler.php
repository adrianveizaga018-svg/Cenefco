<?php

namespace App\Application\Redirecciones\Handlers;

use App\Application\Redirecciones\Commands\UpdateRedireccionCommand;
use App\Application\Redirecciones\DTOs\RedireccionDTO;
use App\Domain\Redirecciones\Contracts\RedireccionRepositoryInterface;

class UpdateRedireccionHandler
{
    public function __construct(private readonly RedireccionRepositoryInterface $repository) {}

    public function handle(UpdateRedireccionCommand $c): RedireccionDTO
    {
        $data = array_filter([
            'url_origen'  => $c->url_origen,
            'url_destino' => $c->url_destino,
            'codigo_http' => $c->codigo_http,
            'activo'      => $c->activo,
            'notas'       => $c->notas,
        ], fn ($v) => $v !== null);

        return $this->repository->update($c->id, $data);
    }
}
