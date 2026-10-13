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
        /** Ventas del periodo sin monto todavía: por negociar, sin tipo de producto o fuera de la tabla */
        public array   $pendientes = [],
    ) {}
}
