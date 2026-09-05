<?php

namespace App\Domain\SueldosDocentes\Contracts;

use App\Application\SueldosDocentes\DTOs\SueldoDocenteDTO;
use App\Application\SueldosDocentes\DTOs\PagoSueldoDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface SueldoDocenteRepositoryInterface
{
    
    public function paginate(PaginationDTO $pagination, array $filters = []): array;

    public function findById(int $id): SueldoDocenteDTO;

    public function create(array $data): SueldoDocenteDTO;

    public function update(int $id, array $data): SueldoDocenteDTO;

    public function delete(int $id): void;

    public function registrarPago(int $idSueldo, array $data): PagoSueldoDTO;

    public function registrarPagoLote(int $idSueldo, array $cuotas): array;

    public function anularPago(int $idSueldo, int $pagoId): void;

    public function docentes(): array;

    public function imparticiones(): array;
}
