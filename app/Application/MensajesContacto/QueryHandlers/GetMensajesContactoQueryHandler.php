<?php

namespace App\Application\MensajesContacto\QueryHandlers;

use App\Application\MensajesContacto\Queries\GetMensajesContactoQuery;
use App\Domain\MensajesContacto\Contracts\MensajeContactoRepositoryInterface;

class GetMensajesContactoQueryHandler
{
    public function __construct(private readonly MensajeContactoRepositoryInterface $repository) {}

    public function handle(GetMensajesContactoQuery $query): array
    {
        return $this->repository->paginate($query->pagination);
    }
}
