<?php

namespace App\Infrastructure\Aliados\Repositories;

use App\Application\Aliados\DTOs\AliadoDTO;
use App\Domain\Aliados\Contracts\AliadoRepositoryInterface;
use App\Domain\Aliados\Exceptions\AliadoNotFoundException;
use App\Infrastructure\Aliados\Models\Aliado;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentAliadoRepository implements AliadoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array
    {
        $q = Aliado::query();

        if ($soloActivos) {
            $q->where('activo', true);
        }

        if ($pagination->query) {
            $q->where('nombre', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy('orden')
            ->orderBy('nombre')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => AliadoDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): AliadoDTO
    {
        $model = Aliado::find($id);
        if (! $model) {
            throw new AliadoNotFoundException($id);
        }

        return AliadoDTO::fromModel($model);
    }

    public function create(array $data): AliadoDTO
    {
        $model = Aliado::create($data);

        return AliadoDTO::fromModel($model);
    }

    public function update(int $id, array $data): AliadoDTO
    {
        $model = Aliado::find($id);
        if (! $model) {
            throw new AliadoNotFoundException($id);
        }
        $model->update($data);

        return AliadoDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return Aliado::destroy($ids) > 0;
    }
}
