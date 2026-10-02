<?php

namespace App\Application\Comisiones\DTOs;

final readonly class ComisionSugeridaDTO
{
    public function __construct(
        public int     $vendedor_id,
        public string  $vendedor_nombre,
        public string  $fecha_desde,
        public string  $fecha_hasta,
        public int     $total_inscritos,
        public float   $monto_comision,
        public array   $inscritos,
    ) {}
}
