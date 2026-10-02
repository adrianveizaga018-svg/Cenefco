<?php

namespace App\Domain\EnviosCertificado\Contracts;

interface EnvioCertificadoRepositoryInterface
{
    public function findByInscripcion(int $idIns): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function delete(int $id): bool;

    public function porPeriodo(?string $fechaInicio, ?string $fechaFin, ?array $idImpPermitidos = null): \Illuminate\Support\Collection;
}
