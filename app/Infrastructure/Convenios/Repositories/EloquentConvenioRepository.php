<?php

namespace App\Infrastructure\Convenios\Repositories;

use App\Application\Convenios\DTOs\ConvenioDTO;
use App\Domain\Convenios\Contracts\ConvenioRepositoryInterface;
use App\Domain\Convenios\Exceptions\ConvenioNotFoundException;
use App\Infrastructure\Convenios\Models\Convenio;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentConvenioRepository implements ConvenioRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array
    {
        $q = Convenio::query();

        if ($soloActivos) {
            $q->where('estado', 'activo');
        }

        if ($pagination->query) {
            $q->where(function ($builder) use ($pagination) {
                $builder->where('nombre', 'like', "%{$pagination->query}%")
                    ->orWhere('institucion', 'like', "%{$pagination->query}%");
            });
        }

        $paginated = $q->orderBy($pagination->sortKey ?: 'orden', $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => ConvenioDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function all(): array
    {
        return Convenio::where('estado', 'activo')
            ->orderBy('orden')
            ->get(['id', 'nombre', 'institucion', 'logo_url'])
            ->map(fn ($m) => [
                'id'          => $m->id,
                'nombre'      => $m->nombre,
                'institucion' => $m->institucion,
                'logo_url'    => $m->logo_url,
            ])
            ->all();
    }

    public function findById(int $id): ConvenioDTO
    {
        $model = Convenio::find($id);
        if (! $model) {
            throw new ConvenioNotFoundException($id);
        }

        return ConvenioDTO::fromModel($model);
    }

    public function create(array $data): ConvenioDTO
    {
        $model = Convenio::create($data);

        return ConvenioDTO::fromModel($model);
    }

    public function update(int $id, array $data): ConvenioDTO
    {
        $model = Convenio::find($id);
        if (! $model) {
            throw new ConvenioNotFoundException($id);
        }
        $model->update($data);

        return ConvenioDTO::fromModel($model->fresh());
    }

    public function delete(int $id): bool
    {
        $model = Convenio::find($id);
        if (! $model) {
            throw new ConvenioNotFoundException($id);
        }

        return (bool) $model->delete();
    }
}
