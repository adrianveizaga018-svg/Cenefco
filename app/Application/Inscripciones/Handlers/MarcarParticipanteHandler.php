<?php

namespace App\Application\Inscripciones\Handlers;

use App\Application\Inscripciones\Commands\MarcarParticipanteCommand;
use App\Application\ListaAprobados\Commands\CreateListaAprobadosCommand;
use App\Application\ListaAprobados\DTOs\ListaAprobadosDTO;
use App\Application\ListaAprobados\Handlers\CreateListaAprobadosHandler;
use App\Domain\Inscripciones\Contracts\InscripcionRepositoryInterface;
use App\Domain\Inscripciones\Exceptions\InscripcionPagoIncompletoException;
use App\Domain\ListaAprobados\Contracts\ListaAprobadosRepositoryInterface;
use App\Domain\ListaAprobados\Exceptions\ParticipanteYaRegistradoException;

class MarcarParticipanteHandler
{
    public function __construct(
        private readonly InscripcionRepositoryInterface    $inscripcionRepository,
        private readonly ListaAprobadosRepositoryInterface  $listaAprobadosRepository,
        private readonly CreateListaAprobadosHandler        $createHandler,
    ) {}

    public function handle(MarcarParticipanteCommand $command): ListaAprobadosDTO
    {
        $elegibilidad = $this->inscripcionRepository->verificarPagoCompleto($command->idIns);

        if (! $elegibilidad['pago_completo']) {
            throw new InscripcionPagoIncompletoException($elegibilidad['pendiente']);
        }

        if ($this->listaAprobadosRepository->existeParaImparteYUsuario($elegibilidad['id_imp'], $elegibilidad['id_us'])) {
            throw new ParticipanteYaRegistradoException();
        }

        return $this->createHandler->handle(new CreateListaAprobadosCommand(
            imparte_id:         $elegibilidad['id_imp'],
            usuario_id:         $elegibilidad['id_us'],
            inscripcion_id:     $command->idIns,
            nota_final:         null,
            nota_minima:        null,
            condicion:          'aprobado',
            observacion:        null,
            comprobante_url:    null,
            ajuste_manual:      false,
            estado_certificado: 'pendiente',
            registrado_por:     $command->idUsReg,
            id_us_reg:          $command->idUsReg,
        ));
    }
}
