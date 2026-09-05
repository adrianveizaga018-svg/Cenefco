<?php

namespace App\Application\Vendedores\DTOs;

final readonly class VendedorComisionDetalleDTO
{
    public function __construct(
        public int     $vendedor_id,
        public string  $vendedor_nombre,
        public float   $total_comision,
        public array   $cursos,
    ) {}
}
