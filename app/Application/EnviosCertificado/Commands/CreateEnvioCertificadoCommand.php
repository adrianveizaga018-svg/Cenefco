<?php

namespace App\Application\EnviosCertificado\Commands;

final readonly class CreateEnvioCertificadoCommand
{
    public function __construct(
        public int     $id_ins,
        public string  $ciudad_destino,
        public string  $fecha_envio,
        public string  $imagen_guia,
        public ?string $aclaraciones = null,
        public ?float  $costo        = null,
        public ?int    $id_us_reg    = null,
    ) {}
}
