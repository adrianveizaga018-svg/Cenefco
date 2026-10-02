<?php

namespace App\Application\EnviosCertificado\Handlers;

use App\Application\EnviosCertificado\Commands\DeleteEnvioCertificadoCommand;
use App\Domain\EnviosCertificado\Contracts\EnvioCertificadoRepositoryInterface;

class DeleteEnvioCertificadoHandler
{
    public function __construct(private readonly EnvioCertificadoRepositoryInterface $repository) {}

    public function handle(DeleteEnvioCertificadoCommand $c): bool
    {
        return $this->repository->delete($c->id);
    }
}
