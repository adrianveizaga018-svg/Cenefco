<?php

namespace App\Application\Vendedores\QueryHandlers;

use App\Application\Vendedores\DTOs\VendedorComisionCursoDTO;
use App\Application\Vendedores\DTOs\VendedorComisionDetalleDTO;
use App\Application\Vendedores\Queries\GetVendedorComisionDetalleQuery;
use App\Domain\Vendedores\Contracts\VendedorRepositoryInterface;
use App\Infrastructure\Vendedores\Services\VendedorComisionEstimadaService;

class GetVendedorComisionDetalleQueryHandler
{
    public function __construct(
        private readonly VendedorRepositoryInterface     $vendedorRepository,
        private readonly VendedorComisionEstimadaService  $comisionEstimadaService,
    ) {}

    public function handle(GetVendedorComisionDetalleQuery $query): VendedorComisionDetalleDTO
    {
        $vendedor = $this->vendedorRepository->findById($query->vendedorId);

        $cursos = $this->comisionEstimadaService
            ->cursosPorVendedor($query->vendedorId)
            ->map(fn ($row) => VendedorComisionCursoDTO::fromRow($row))
            ->all();

        $totalComision = array_sum(array_map(fn (VendedorComisionCursoDTO $c) => $c->comision_estimada, $cursos));

        return new VendedorComisionDetalleDTO(
            vendedor_id:     $vendedor->id,
            vendedor_nombre: trim("{$vendedor->nombre} {$vendedor->apellido}"),
            total_comision:  $totalComision,
            cursos:          $cursos,
        );
    }
}
