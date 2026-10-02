<?php

namespace App\Infrastructure\HitosInstitucionales\Repositories;

use App\Application\HitosInstitucionales\DTOs\HitoInstitucionalDTO;
use App\Domain\HitosInstitucionales\Contracts\HitoInstitucionalRepositoryInterface;
use App\Domain\HitosInstitucionales\Exceptions\HitoInstitucionalNotFoundException;
use App\Infrastructure\HitosInstitucionales\Models\HitoInstitucional;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentHitoInstitucionalRepository implements HitoInstitucionalRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array
    {
        $q = HitoInstitucional::query();

        if ($soloActivos) {
            $q->where('activo', true);
        }

        if ($pagination->query) {
            $q->where(function ($sq) use ($pagination) {
                $sq->where('titulo', 'like', "%{$pagination->query}%")
                   ->orWhere('anio', 'like', "%{$pagination->query}%");
            });
        }

        $paginated = $q->orderBy('orden')
            ->orderBy('anio')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => HitoInstitucionalDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): HitoInstitucionalDTO
    {
        $model = HitoInstitucional::find($id);
        if (! $model) {
            throw new HitoInstitucionalNotFoundException($id);
        }

        return HitoInstitucionalDTO::fromModel($model);
    }

    public function create(array $data): HitoInstitucionalDTO
    {
        return HitoInstitucionalDTO::fromModel(HitoInstitucional::create($data));
    }

    public function update(int $id, array $data): HitoInstitucionalDTO
    {
        $model = HitoInstitucional::find($id);
        if (! $model) {
            throw new HitoInstitucionalNotFoundException($id);
        }
        $model->update($data);

        return HitoInstitucionalDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return HitoInstitucional::destroy($ids) > 0;
    }
}
