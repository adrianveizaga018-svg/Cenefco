<?php

namespace App\Application\MensajesContacto\QueryHandlers;

use App\Application\MensajesContacto\Queries\GetMensajeContactoByIdQuery;
use App\Domain\MensajesContacto\Contracts\MensajeContactoRepositoryInterface;

class GetMensajeContactoByIdQueryHandler
{
    public function __construct(private readonly MensajeContactoRepositoryInterface $repository) {}

    public function handle(GetMensajeContactoByIdQuery $query): mixed
    {
        return $this->repository->findById($query->id);
    }
}
