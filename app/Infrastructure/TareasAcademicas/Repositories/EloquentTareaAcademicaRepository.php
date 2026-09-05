<?php

namespace App\Infrastructure\TareasAcademicas\Repositories;

use App\Application\TareasAcademicas\DTOs\TareaAcademicaDTO;
use App\Domain\TareasAcademicas\Repositories\TareaAcademicaRepositoryInterface;
use App\Infrastructure\TareasAcademicas\Models\TareaAcademica;
use Illuminate\Support\Collection;

class EloquentTareaAcademicaRepository implements TareaAcademicaRepositoryInterface
{
    public function getByProgramaId(int $programaId): Collection
    {
        return TareaAcademica::where('programa_id', $programaId)
            ->get()
            ->map(fn($m) => TareaAcademicaDTO::fromModel($m));
    }

    public function findById(int $id): ?TareaAcademicaDTO
    {
        $model = TareaAcademica::find($id);
        return $model ? TareaAcademicaDTO::fromModel($model) : null;
    }

    public function create(array $data): TareaAcademicaDTO
    {
        $model = TareaAcademica::create($data);
        return TareaAcademicaDTO::fromModel($model);
    }

    public function update(int $id, array $data): TareaAcademicaDTO
    {
        $model = TareaAcademica::findOrFail($id);
        $model->update($data);
        return TareaAcademicaDTO::fromModel($model);
    }
}
