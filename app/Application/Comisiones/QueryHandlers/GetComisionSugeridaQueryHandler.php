<?php

namespace App\Application\Comisiones\QueryHandlers;

use App\Application\Comisiones\DTOs\ComisionSugeridaDTO;
use App\Application\Comisiones\Queries\GetComisionSugeridaQuery;
use App\Application\Comisiones\Services\ComisionCalculadorService;
use App\Domain\Vendedores\Contracts\VendedorRepositoryInterface;

class GetComisionSugeridaQueryHandler
{
    public function __construct(
        private readonly VendedorRepositoryInterface $vendedorRepository,
        private readonly ComisionCalculadorService     $calculador,
    ) {}

    public function handle(GetComisionSugeridaQuery $query): ComisionSugeridaDTO
    {
        $vendedor = $this->vendedorRepository->findById($query->vendedorId);

        $calculo = $this->calculador->calcular(
            $query->vendedorId,
            $query->fechaDesde,
            $query->fechaHasta,
        );

        return new ComisionSugeridaDTO(
            vendedor_id:      $query->vendedorId,
            vendedor_nombre:  trim($vendedor->nombre.' '.$vendedor->apellido),
            fecha_desde:      $query->fechaDesde,
            fecha_hasta:      $query->fechaHasta,
            total_inscritos:  $calculo['total_inscritos'],
            monto_comision:   $calculo['monto_comision'],
            inscritos:        $calculo['inscritos'],
        );
    }
}
