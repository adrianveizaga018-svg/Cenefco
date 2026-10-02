<?php

namespace App\Application\DocumentosTransparencia\Handlers;

use App\Application\DocumentosTransparencia\Commands\CreateDocumentoCommand;
use App\Application\DocumentosTransparencia\DTOs\DocumentoDTO;
use App\Domain\DocumentosTransparencia\Contracts\DocumentoRepositoryInterface;

class CreateDocumentoHandler
{
    public function __construct(private readonly DocumentoRepositoryInterface $repository) {}

    public function handle(CreateDocumentoCommand $command): DocumentoDTO
    {
        return $this->repository->create([
            'tipo_documento_id' => $command->tipo_documento_id,
            'secretaria_id' => $command->secretaria_id,
            'publicado_por' => $command->publicado_por,
            'titulo' => $command->titulo,
            'descripcion' => $command->descripcion,
            'archivo_url' => $command->archivo_url,
            'gestion' => $command->gestion,
            'fecha_publicacion' => $command->fecha_publicacion,
            'activo' => $command->activo,
        ]);
    }
}
