<?php

namespace App\Domain\Ventas\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface VentaRepositoryInterface
{
    
    public function paginate(PaginationDTO $pagination, array $filters = []): array;

    public function findById(int $id): object;

    public function reporte(array $filters = []): array;

    public function reportePorVendedor(array $filtros = []): array;

    public function reportePorCanal(array $filtros = []): array;

    public function proyeccionCobros(int $meses = 6): array;
}
