<?php

namespace App\Domain\TareasAcademicas\Repositories;

use App\Application\TareasAcademicas\DTOs\TareaAcademicaDTO;
use Illuminate\Support\Collection;

interface TareaAcademicaRepositoryInterface
{
    public function getByProgramaId(int $programaId): Collection;
    public function findById(int $id): ?TareaAcademicaDTO;
    public function create(array $data): TareaAcademicaDTO;
    public function update(int $id, array $data): TareaAcademicaDTO;
}
