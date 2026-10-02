<?php

namespace App\Application\ListaAprobados\Commands;

final readonly class CreateListaAprobadosCommand
{
    public function __construct(
        public int     $imparte_id,
        public int     $usuario_id,
        public ?int    $inscripcion_id,
        public ?float  $nota_final,
        public ?float  $nota_minima,
        public string  $condicion,
        public ?string $observacion,
        public ?string $comprobante_url,
        public bool    $ajuste_manual,
        public string  $estado_certificado,
        public ?int    $registrado_por,
        public int     $id_us_reg,
    ) {}
}
