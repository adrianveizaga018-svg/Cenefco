<?php

namespace App\Application\InscripcionesDiplomado\Commands;

final readonly class CreateInscripcionDiplomadoCommand
{
    public function __construct(
        public string  $nombre,
        public string  $email,
        public ?int    $programa_id              = null,
        public ?string $apellido_paterno         = null,
        public ?string $apellido_materno         = null,
        public ?string $fecha_nacimiento          = null,
        public ?string $ci                        = null,
        public ?int    $expedido_id               = null,
        public ?string $telefono_grupo_inscritos  = null,
        public ?string $archivo_ci                = null,
        public ?string $archivo_titulo            = null,
        public ?string $archivo_cv                = null,
        public ?string $archivo_foto_3x3          = null,
        public ?int    $ciudad_residencia_id      = null,
        public ?string $provincia_especificar     = null,
        public ?string $medio_pago                = null,
        public ?int    $medio_pago_id             = null,
        public ?float  $monto_pagado              = null,
        public ?string $archivo_comprobante_pago  = null,
        public ?string $sugerencia_curso          = null,
        public bool    $recomendar_docente        = false,
        public ?string $detalle_docente           = null,
        public ?string $origen                    = null,
        public ?string $ip_origen                 = null,
    ) {}
}
