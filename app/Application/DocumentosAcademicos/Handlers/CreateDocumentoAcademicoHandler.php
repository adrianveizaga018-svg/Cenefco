<?php

namespace App\Application\DocumentosAcademicos\Handlers;

use App\Application\DocumentosAcademicos\Commands\CreateDocumentoAcademicoCommand;
use App\Application\DocumentosAcademicos\DTOs\DocumentoAcademicoDTO;
use App\Domain\DocumentosAcademicos\Contracts\DocumentoAcademicoRepositoryInterface;

class CreateDocumentoAcademicoHandler
{
    public function __construct(
        private readonly DocumentoAcademicoRepositoryInterface $repository
    ) {}

    public function handle(CreateDocumentoAcademicoCommand $command): DocumentoAcademicoDTO
    {
        return $this->repository->create([
            'id_documento'          => $command->id_documento,
            'id_us_reg'             => $command->id_us_reg ?? 0,
            'num_documento'         => $command->num_documento ?? 0,
            'id_us'                 => $command->id_us,
            'id_fechapago'          => $command->id_fechapago,
            'id_fechadoc'           => $command->id_fechadoc,
            'fecha_dejo_fisico'     => $command->fecha_dejo_fisico,
            'dejo_documento_fisico' => $command->dejo_documento_fisico,
            'documento_digital'     => $command->documento_digital,
            'observacion_doc'       => $command->observacion_doc,
            'estado'                => $command->estado,
            'fecha_reg'             => now(),
        ]);
    }
}
