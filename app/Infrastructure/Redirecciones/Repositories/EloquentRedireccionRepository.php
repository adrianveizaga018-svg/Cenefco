<?php

namespace App\Infrastructure\Redirecciones\Repositories;

use App\Application\Redirecciones\DTOs\RedireccionDTO;
use App\Domain\Redirecciones\Contracts\RedireccionRepositoryInterface;
use App\Domain\Redirecciones\Exceptions\RedireccionNotFoundException;
use App\Infrastructure\Redirecciones\Models\Redireccion;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentRedireccionRepository implements RedireccionRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = Redireccion::query();

        if ($pagination->query) {
            $term = '%' . $pagination->query . '%';
            $q->where(function ($sq) use ($term) {
                $sq->where('url_origen', 'like', $term)
                   ->orWhere('url_destino', 'like', $term);
            });
        }

        $paginated = $q->orderBy('url_origen', 'asc')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => RedireccionDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): RedireccionDTO
    {
        $model = Redireccion::find($id);
        if (! $model) {
            throw new RedireccionNotFoundException($id);
        }

        return RedireccionDTO::fromModel($model);
    }

    public function create(array $data): RedireccionDTO
    {
        return RedireccionDTO::fromModel(Redireccion::create($data));
    }

    public function update(int $id, array $data): RedireccionDTO
    {
        $model = Redireccion::find($id);
        if (! $model) {
            throw new RedireccionNotFoundException($id);
        }
        $model->update($data);

        return RedireccionDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return Redireccion::destroy($ids) > 0;
    }
}
