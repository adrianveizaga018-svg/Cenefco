<?php

namespace App\Infrastructure\ManualesInstitucionales\Repositories;

use App\Application\ManualesInstitucionales\DTOs\ManualInstitucionalDTO;
use App\Domain\ManualesInstitucionales\Contracts\ManualInstitucionalRepositoryInterface;
use App\Infrastructure\ManualesInstitucionales\Models\ManualInstitucional;

class EloquentManualInstitucionalRepository implements ManualInstitucionalRepositoryInterface
{
    public function all(bool $soloActivos = false): array
    {
        $q = ManualInstitucional::orderBy('gestion', 'desc')->orderBy('titulo');

        if ($soloActivos) {
            $q->where('vigente', true);
        }

        return $q->get()->map(fn ($m) => ManualInstitucionalDTO::fromModel($m))->all();
    }

    public function findById(int $id): ManualInstitucionalDTO
    {
        $model = ManualInstitucional::find($id);
        if (! $model) {
            throw new \App\Domain\ManualesInstitucionales\Exceptions\ManualInstitucionalNotFoundException($id);
        }

        return ManualInstitucionalDTO::fromModel($model);
    }

    public function create(array $data): ManualInstitucionalDTO
    {
        $model = ManualInstitucional::create($data);

        return ManualInstitucionalDTO::fromModel($model);
    }

    public function update(int $id, array $data): ManualInstitucionalDTO
    {
        
        $model = ManualInstitucional::find($id);
        if (! $model) {
            throw new \App\Domain\ManualesInstitucionales\Exceptions\ManualInstitucionalNotFoundException($id);
        }
        $model->update($data);

        return ManualInstitucionalDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return ManualInstitucional::destroy($ids) > 0;
    }
}
