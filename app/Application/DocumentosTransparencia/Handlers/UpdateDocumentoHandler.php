<?php

namespace App\Application\DocumentosTransparencia\Handlers;

use App\Application\DocumentosTransparencia\Commands\UpdateDocumentoCommand;
use App\Application\DocumentosTransparencia\DTOs\DocumentoDTO;
use App\Domain\DocumentosTransparencia\Contracts\DocumentoRepositoryInterface;

class UpdateDocumentoHandler
{
    public function __construct(private readonly DocumentoRepositoryInterface $repository) {}

    public function handle(UpdateDocumentoCommand $command): DocumentoDTO
    {
        return $this->repository->update($command->id, $command->data);
    }
}
