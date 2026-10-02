<?php

namespace App\Application\DocumentosAcademicos\Handlers;

use App\Application\DocumentosAcademicos\Commands\UpdateDocumentoAcademicoCommand;
use App\Application\DocumentosAcademicos\DTOs\DocumentoAcademicoDTO;
use App\Domain\DocumentosAcademicos\Contracts\DocumentoAcademicoRepositoryInterface;

class UpdateDocumentoAcademicoHandler
{
    public function __construct(
        private readonly DocumentoAcademicoRepositoryInterface $repository
    ) {}

    public function handle(UpdateDocumentoAcademicoCommand $command): DocumentoAcademicoDTO
    {
        return $this->repository->update($command->id, [
            'id_fechapago'          => $command->id_fechapago,
            'id_fechadoc'           => $command->id_fechadoc,
            'fecha_dejo_fisico'     => $command->fecha_dejo_fisico,
            'dejo_documento_fisico' => $command->dejo_documento_fisico,
            'documento_digital'     => $command->documento_digital,
            'observacion_doc'       => $command->observacion_doc,
            'estado'                => $command->estado,
        ]);
    }
}
