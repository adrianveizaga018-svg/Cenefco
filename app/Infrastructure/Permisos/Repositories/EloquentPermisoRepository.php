<?php

namespace App\Infrastructure\Permisos\Repositories;

use App\Application\Permisos\DTOs\PermisoDTO;
use App\Domain\Permisos\Contracts\PermisoRepositoryInterface;
use App\Domain\Permisos\Exceptions\PermisoNotFoundException;
use App\Infrastructure\Permisos\Models\Permiso;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentPermisoRepository implements PermisoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = Permiso::query();

        if ($pagination->query) {
            $q->where('codigo', 'like', "%{$pagination->query}%")
                ->orWhere('modulo', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy($pagination->sortKey ?? 'codigo', $pagination->sortOrder ?? 'asc')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($r) => PermisoDTO::fromModel($r))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): PermisoDTO
    {
        $model = Permiso::find($id);
        if (! $model) {
            throw new PermisoNotFoundException($id);
        }

        return PermisoDTO::fromModel($model);
    }

    public function create(array $data): Permiso
    {
        return Permiso::create($data);
    }

    public function update(int $id, array $data): PermisoDTO
    {
        $model = Permiso::find($id);
        if (! $model) {
            throw new PermisoNotFoundException($id);
        }
        $model->update($data);

        return PermisoDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        if (is_array($ids)) {
            Permiso::whereIn('id', $ids)->delete();
        } else {
            $model = Permiso::find($ids);
            if (! $model) {
                throw new PermisoNotFoundException($ids);
            }
            $model->delete();
        }

        return true;
    }
}
