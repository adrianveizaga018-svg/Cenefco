<?php

namespace App\Application\InscripcionesDiplomado\Handlers;

use App\Application\InscripcionesDiplomado\Commands\UpdateInscripcionDiplomadoCommand;
use App\Application\InscripcionesDiplomado\DTOs\InscripcionDiplomadoDTO;
use App\Domain\InscripcionesDiplomado\Contracts\InscripcionDiplomadoRepositoryInterface;
use App\Services\DocumentoEstudianteArchivador;

class UpdateInscripcionDiplomadoHandler
{
    public function __construct(
        private readonly InscripcionDiplomadoRepositoryInterface $repository,
        private readonly DocumentoEstudianteArchivador $archivador,
    ) {}

    public function handle(UpdateInscripcionDiplomadoCommand $command): InscripcionDiplomadoDTO
    {
        $archivoCi              = $command->archivo_ci;
        $archivoTitulo          = $command->archivo_titulo;
        $archivoCv              = $command->archivo_cv;
        $archivoFoto3x3         = $command->archivo_foto_3x3;
        $archivoComprobantePago = $command->archivo_comprobante_pago;

        $hayArchivoNuevo = $archivoCi || $archivoTitulo || $archivoCv || $archivoFoto3x3 || $archivoComprobantePago;

        if ($hayArchivoNuevo) {
            $actual = $this->repository->findById($command->id);
            $ci         = $command->ci ?? $actual->ci;
            $programaId = $command->programa_id ?? $actual->programa_id;

            if ($ci && $programaId) {
                $archivoCi              = $this->archivador->archivar($archivoCi, $ci, 'diplomado', $programaId, 'archivo_ci');
                $archivoTitulo          = $this->archivador->archivar($archivoTitulo, $ci, 'diplomado', $programaId, 'archivo_titulo');
                $archivoCv              = $this->archivador->archivar($archivoCv, $ci, 'diplomado', $programaId, 'archivo_cv');
                $archivoFoto3x3         = $this->archivador->archivar($archivoFoto3x3, $ci, 'diplomado', $programaId, 'archivo_foto_3x3');
                $archivoComprobantePago = $this->archivador->archivar($archivoComprobantePago, $ci, 'diplomado', $programaId, 'archivo_comprobante_pago');
            }
        }

        $data = array_filter([
            'estado'                    => $command->estado,
            'notificado'                => $command->notificado,
            'nombre'                    => $command->nombre,
            'apellido_paterno'          => $command->apellido_paterno,
            'apellido_materno'          => $command->apellido_materno,
            'fecha_nacimiento'          => $command->fecha_nacimiento,
            'email'                     => $command->email,
            'ci'                        => $command->ci,
            'expedido_id'               => $command->expedido_id,
            'telefono_grupo_inscritos'  => $command->telefono_grupo_inscritos,
            'archivo_ci'                => $archivoCi,
            'archivo_titulo'            => $archivoTitulo,
            'archivo_cv'                => $archivoCv,
            'archivo_foto_3x3'          => $archivoFoto3x3,
            'ciudad_residencia_id'      => $command->ciudad_residencia_id,
            'provincia_especificar'     => $command->provincia_especificar,
            'medio_pago'                => $command->medio_pago,
            'medio_pago_id'             => $command->medio_pago_id,
            'monto_pagado'              => $command->monto_pagado,
            'archivo_comprobante_pago'  => $archivoComprobantePago,
            'sugerencia_curso'          => $command->sugerencia_curso,
            'recomendar_docente'        => $command->recomendar_docente,
            'detalle_docente'           => $command->detalle_docente,
            'programa_id'               => $command->programa_id,
        ], fn ($v) => $v !== null);

        if (isset($data['notificado']) && $data['notificado']) {
            $data['fecha_notificacion'] = now()->toDateTimeString();
        }

        return $this->repository->update($command->id, $data);
    }
}
