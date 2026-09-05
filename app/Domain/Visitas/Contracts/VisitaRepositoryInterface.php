<?php

namespace App\Domain\Visitas\Contracts;

interface VisitaRepositoryInterface
{
    public function create(array $data): mixed;
    public function stats(string $desde, string $hasta): array;
}
