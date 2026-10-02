<?php

namespace App\Application\DocumentosTransparencia\Handlers;

use App\Application\DocumentosTransparencia\Commands\DeleteDocumentoCommand;
use App\Domain\DocumentosTransparencia\Contracts\DocumentoRepositoryInterface;

class DeleteDocumentoHandler
{
    public function __construct(private readonly DocumentoRepositoryInterface $repository) {}

    public function handle(DeleteDocumentoCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
