<?php

namespace App\Infrastructure\CifrasInstitucionales\Repositories;

use App\Application\CifrasInstitucionales\DTOs\CifraInstitucionalDTO;
use App\Domain\CifrasInstitucionales\Contracts\CifraInstitucionalRepositoryInterface;
use App\Domain\CifrasInstitucionales\Exceptions\CifraInstitucionalNotFoundException;
use App\Infrastructure\CifrasInstitucionales\Models\CifraInstitucional;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentCifraInstitucionalRepository implements CifraInstitucionalRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = CifraInstitucional::query();

        if ($pagination->query) {
            $q->where(function ($sq) use ($pagination) {
                $sq->where('etiqueta', 'like', "%{$pagination->query}%")
                   ->orWhere('valor', 'like', "%{$pagination->query}%");
            });
        }

        $paginated = $q->orderBy('orden')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => CifraInstitucionalDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function getAllActivas(): array
    {
        return CifraInstitucional::where('activo', true)
            ->orderBy('orden')
            ->get()
            ->map(fn ($m) => CifraInstitucionalDTO::fromModel($m))
            ->all();
    }

    public function findById(int $id): CifraInstitucionalDTO
    {
        $model = CifraInstitucional::find($id);
        if (! $model) {
            throw new CifraInstitucionalNotFoundException($id);
        }

        return CifraInstitucionalDTO::fromModel($model);
    }

    public function create(array $data): CifraInstitucionalDTO
    {
        return CifraInstitucionalDTO::fromModel(CifraInstitucional::create($data));
    }

    public function update(int $id, array $data): CifraInstitucionalDTO
    {
        $model = CifraInstitucional::find($id);
        if (! $model) {
            throw new CifraInstitucionalNotFoundException($id);
        }
        $model->update($data);

        return CifraInstitucionalDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return CifraInstitucional::destroy($ids) > 0;
    }
}
