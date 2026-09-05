<?php

namespace App\Application\Inscripciones\Handlers;

use App\Application\Inscripciones\Commands\MarcarParticipanteCommand;
use App\Application\Inscripciones\Commands\MarcarParticipantesBulkCommand;
use App\Domain\Inscripciones\Exceptions\InscripcionNotFoundException;
use App\Domain\Inscripciones\Exceptions\InscripcionPagoIncompletoException;
use App\Domain\ListaAprobados\Exceptions\ParticipanteYaRegistradoException;

class MarcarParticipantesBulkHandler
{
    public function __construct(
        private readonly MarcarParticipanteHandler $marcarHandler,
    ) {}

    public function handle(MarcarParticipantesBulkCommand $command): array
    {
        $registrados = [];
        $omitidos    = [];

        foreach (array_unique($command->idsIns) as $idIns) {
            try {
                $registrados[] = $this->marcarHandler->handle(new MarcarParticipanteCommand(
                    idIns:   (int) $idIns,
                    idUsReg: $command->idUsReg,
                ));
            } catch (InscripcionPagoIncompletoException|ParticipanteYaRegistradoException|InscripcionNotFoundException $e) {
                $omitidos[] = ['id_ins' => (int) $idIns, 'motivo' => $e->getMessage()];
            }
        }

        return ['registrados' => $registrados, 'omitidos' => $omitidos];
    }
}
