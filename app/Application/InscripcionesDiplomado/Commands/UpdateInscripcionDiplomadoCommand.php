<?php

namespace App\Application\InscripcionesDiplomado\Commands;

final readonly class UpdateInscripcionDiplomadoCommand
{
    public function __construct(
        public int     $id,
        public ?string $estado                    = null,
        public ?bool   $notificado                = null,
        public ?string $nombre                     = null,
        public ?string $apellido_paterno            = null,
        public ?string $apellido_materno            = null,
        public ?string $fecha_nacimiento            = null,
        public ?string $email                      = null,
        public ?string $ci                          = null,
        public ?int    $expedido_id                 = null,
        public ?string $telefono_grupo_inscritos    = null,
        public ?string $archivo_ci                  = null,
        public ?string $archivo_titulo              = null,
        public ?string $archivo_cv                  = null,
        public ?string $archivo_foto_3x3            = null,
        public ?int    $ciudad_residencia_id        = null,
        public ?string $provincia_especificar       = null,
        public ?string $medio_pago                  = null,
        public ?int    $medio_pago_id               = null,
        public ?float  $monto_pagado                = null,
        public ?string $archivo_comprobante_pago     = null,
        public ?string $sugerencia_curso             = null,
        public ?bool   $recomendar_docente           = null,
        public ?string $detalle_docente              = null,
        public ?int    $programa_id                  = null,
    ) {}
}
