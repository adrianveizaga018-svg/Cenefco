<?php

namespace App\Application\Ventas\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetVentasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $estadoPago   = null,
        public ?string       $periodo      = null,
        public ?string       $gestion      = null,
        public bool          $conInactivos = false,
        public ?string       $canalVenta   = null,
        public ?int          $idVendedor   = null,
    ) {}
}
