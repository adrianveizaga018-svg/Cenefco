<?php

namespace App\Infrastructure\Comunicados\Repositories;

use App\Application\Comunicados\DTOs\ComunicadoDTO;
use App\Domain\Comunicados\Contracts\ComunicadoRepositoryInterface;
use App\Domain\Comunicados\Exceptions\ComunicadoNotFoundException;
use App\Infrastructure\Comunicados\Models\Comunicado;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentComunicadoRepository implements ComunicadoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array
    {
        $q = Comunicado::query();

        if ($soloActivos) {
            $q->where('estado', 'publicado');
        }

        if ($pagination->query) {
            $q->where(function ($builder) use ($pagination) {
                $builder->where('titulo', 'like', "%{$pagination->query}%")
                    ->orWhere('resumen', 'like', "%{$pagination->query}%");
            });
        }

        $paginated = $q->orderBy('created_at', 'desc')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => ComunicadoDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): ComunicadoDTO
    {
        $model = Comunicado::find($id);
        if (! $model) {
            throw new ComunicadoNotFoundException($id);
        }

        return ComunicadoDTO::fromModel($model);
    }

    public function findBySlug(string $slug): ComunicadoDTO
    {
        $model = Comunicado::where('slug', $slug)->first();
        if (! $model) {
            throw new ComunicadoNotFoundException($slug);
        }

        return ComunicadoDTO::fromModel($model);
    }

    public function create(array $data): ComunicadoDTO
    {
        return ComunicadoDTO::fromModel(Comunicado::create($data));
    }

    public function update(int $id, array $data): ComunicadoDTO
    {
        $model = Comunicado::find($id);
        if (! $model) {
            throw new ComunicadoNotFoundException($id);
        }
        $model->update($data);

        return ComunicadoDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return Comunicado::destroy($ids) > 0;
    }
}
