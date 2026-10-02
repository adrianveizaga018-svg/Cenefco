<?php

namespace App\Infrastructure\PreguntasFrecuentes\Repositories;

use App\Application\PreguntasFrecuentes\DTOs\PreguntaFrecuenteDTO;
use App\Domain\PreguntasFrecuentes\Contracts\PreguntaFrecuenteRepositoryInterface;
use App\Domain\PreguntasFrecuentes\Exceptions\PreguntaFrecuenteNotFoundException;
use App\Infrastructure\PreguntasFrecuentes\Models\PreguntaFrecuente;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentPreguntaFrecuenteRepository implements PreguntaFrecuenteRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array
    {
        $q = PreguntaFrecuente::query();

        if ($soloActivos) {
            $q->where('activo', true);
        }

        if ($pagination->query) {
            $q->where(function ($sub) use ($pagination) {
                $sub->where('pregunta', 'like', "%{$pagination->query}%")
                    ->orWhere('respuesta', 'like', "%{$pagination->query}%");
            });
        }

        $paginated = $q->orderBy($pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => PreguntaFrecuenteDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): PreguntaFrecuenteDTO
    {
        $model = PreguntaFrecuente::find($id);
        if (! $model) {
            throw new PreguntaFrecuenteNotFoundException($id);
        }

        return PreguntaFrecuenteDTO::fromModel($model);
    }

    public function create(array $data): PreguntaFrecuenteDTO
    {
        $model = PreguntaFrecuente::create($data);

        return PreguntaFrecuenteDTO::fromModel($model);
    }

    public function update(int $id, array $data): PreguntaFrecuenteDTO
    {
        $model = PreguntaFrecuente::find($id);
        if (! $model) {
            throw new PreguntaFrecuenteNotFoundException($id);
        }
        $model->update($data);

        return PreguntaFrecuenteDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return PreguntaFrecuente::destroy($ids) > 0;
    }
}
