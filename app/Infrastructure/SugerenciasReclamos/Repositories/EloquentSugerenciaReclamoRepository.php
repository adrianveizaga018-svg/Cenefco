<?php

namespace App\Infrastructure\SugerenciasReclamos\Repositories;

use App\Application\SugerenciasReclamos\DTOs\SugerenciaReclamoDTO;
use App\Domain\SugerenciasReclamos\Contracts\SugerenciaReclamoRepositoryInterface;
use App\Domain\SugerenciasReclamos\Exceptions\SugerenciaReclamoNotFoundException;
use App\Infrastructure\SugerenciasReclamos\Models\SugerenciaReclamo;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentSugerenciaReclamoRepository implements SugerenciaReclamoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = SugerenciaReclamo::query();

        if ($pagination->query) {
            $q->where('asunto', 'like', "%{$pagination->query}%")
                ->orWhere('mensaje', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy($pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => SugerenciaReclamoDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): SugerenciaReclamoDTO
    {
        $model = SugerenciaReclamo::find($id);
        if (! $model) {
            throw new SugerenciaReclamoNotFoundException($id);
        }

        return SugerenciaReclamoDTO::fromModel($model);
    }

    public function create(array $data): SugerenciaReclamoDTO
    {
        $model = SugerenciaReclamo::create($data);

        return SugerenciaReclamoDTO::fromModel($model);
    }

    public function update(int $id, array $data): SugerenciaReclamoDTO
    {
        $model = SugerenciaReclamo::find($id);
        if (! $model) {
            throw new SugerenciaReclamoNotFoundException($id);
        }
        $model->update($data);

        return SugerenciaReclamoDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return SugerenciaReclamo::destroy($ids) > 0;
    }
}
