<?php

namespace App\Application\InscripcionesDiplomado\Handlers;

use App\Application\InscripcionesDiplomado\Commands\CreateInscripcionDiplomadoCommand;
use App\Application\InscripcionesDiplomado\DTOs\InscripcionDiplomadoDTO;
use App\Domain\InscripcionesDiplomado\Contracts\InscripcionDiplomadoRepositoryInterface;
use App\Domain\InscripcionesDiplomado\Exceptions\InscripcionDiplomadoDuplicadaException;
use App\Services\DocumentoEstudianteArchivador;

class CreateInscripcionDiplomadoHandler
{
    public function __construct(
        private readonly InscripcionDiplomadoRepositoryInterface $repository,
        private readonly DocumentoEstudianteArchivador $archivador,
    ) {}

    public function handle(CreateInscripcionDiplomadoCommand $command): InscripcionDiplomadoDTO
    {
        if ($command->ci) {
            $mensajeDuplicado = $this->repository->buscarDuplicado($command->ci, $command->programa_id);
            if ($mensajeDuplicado) {
                throw new InscripcionDiplomadoDuplicadaException($mensajeDuplicado);
            }
        }

        $archivoCi              = $command->archivo_ci;
        $archivoTitulo          = $command->archivo_titulo;
        $archivoCv              = $command->archivo_cv;
        $archivoFoto3x3         = $command->archivo_foto_3x3;
        $archivoComprobantePago = $command->archivo_comprobante_pago;

        if ($command->ci && $command->programa_id) {
            $archivoCi              = $this->archivador->archivar($archivoCi, $command->ci, 'diplomado', $command->programa_id, 'archivo_ci');
            $archivoTitulo          = $this->archivador->archivar($archivoTitulo, $command->ci, 'diplomado', $command->programa_id, 'archivo_titulo');
            $archivoCv              = $this->archivador->archivar($archivoCv, $command->ci, 'diplomado', $command->programa_id, 'archivo_cv');
            $archivoFoto3x3         = $this->archivador->archivar($archivoFoto3x3, $command->ci, 'diplomado', $command->programa_id, 'archivo_foto_3x3');
            $archivoComprobantePago = $this->archivador->archivar($archivoComprobantePago, $command->ci, 'diplomado', $command->programa_id, 'archivo_comprobante_pago');
        }

        $dto = $this->repository->create([
            'programa_id'               => $command->programa_id,
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
            'origen'                    => $command->origen,
            'estado'                    => 'pendiente',
            'ip_origen'                 => $command->ip_origen,
        ]);

        try {
            $this->repository->convertirInscripcion($dto->id, null, null);
            return $this->repository->findById($dto->id);
        } catch (\RuntimeException $e) {
            report($e);
            return $dto;
        }
    }
}
