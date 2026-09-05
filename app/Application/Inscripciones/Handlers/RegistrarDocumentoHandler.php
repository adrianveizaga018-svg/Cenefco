<?php

namespace App\Application\Inscripciones\Handlers;

use App\Application\Inscripciones\Commands\RegistrarDocumentoCommand;
use App\Domain\Inscripciones\Contracts\InscripcionRepositoryInterface;
use App\Domain\Inscripciones\Exceptions\InscripcionNotFoundException;

class RegistrarDocumentoHandler
{
    public function __construct(
        private readonly InscripcionRepositoryInterface $repository
    ) {}

    public function handle(RegistrarDocumentoCommand $command): object
    {
        $ins = $this->repository->findById($command->id_ins);

        return $this->repository->upsertDocumento($ins->id_us, $command->id_fechadoc, [
            'dejo_documento_fisico' => $command->dejo_documento_fisico ? 1 : 0,
            'fecha_dejo_fisico'     => $command->dejo_documento_fisico ? now()->toDateString() : null,
            'documento_digital'     => $command->documento_digital,
            'observacion_doc'       => $command->observacion_doc,
            'id_us_reg'             => $command->id_us_reg,
        ]);
    }
}
