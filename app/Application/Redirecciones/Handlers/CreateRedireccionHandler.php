<?php

namespace App\Application\Redirecciones\Handlers;

use App\Application\Redirecciones\Commands\CreateRedireccionCommand;
use App\Application\Redirecciones\DTOs\RedireccionDTO;
use App\Domain\Redirecciones\Contracts\RedireccionRepositoryInterface;

class CreateRedireccionHandler
{
    public function __construct(private readonly RedireccionRepositoryInterface $repository) {}

    public function handle(CreateRedireccionCommand $c): RedireccionDTO
    {
        return $this->repository->create([
            'url_origen'  => $c->url_origen,
            'url_destino' => $c->url_destino,
            'codigo_http' => $c->codigo_http,
            'activo'      => $c->activo,
            'notas'       => $c->notas,
        ]);
    }
}
