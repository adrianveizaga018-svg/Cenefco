<?php

namespace App\Infrastructure\Etiquetas\Repositories;

use App\Application\Etiquetas\DTOs\EtiquetaDTO;
use App\Domain\Etiquetas\Contracts\EtiquetaRepositoryInterface;
use App\Domain\Etiquetas\Exceptions\EtiquetaNotFoundException;
use App\Infrastructure\Etiquetas\Models\Etiqueta;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentEtiquetaRepository implements EtiquetaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = Etiqueta::query();

        if ($pagination->query) {
            $q->where('nombre', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy($pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => EtiquetaDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): EtiquetaDTO
    {
        $model = Etiqueta::find($id);
        if (! $model) {
            throw new EtiquetaNotFoundException($id);
        }

        return EtiquetaDTO::fromModel($model);
    }

    public function findBySlug(string $slug): EtiquetaDTO
    {
        $model = Etiqueta::where('slug', $slug)->first();
        if (! $model) {
            throw new EtiquetaNotFoundException($slug);
        }

        return EtiquetaDTO::fromModel($model);
    }

    public function create(array $data): EtiquetaDTO
    {
        $model = Etiqueta::create($data);

        return EtiquetaDTO::fromModel($model);
    }

    public function update(int $id, array $data): EtiquetaDTO
    {
        $model = Etiqueta::find($id);
        if (! $model) {
            throw new EtiquetaNotFoundException($id);
        }
        $model->update($data);

        return EtiquetaDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return Etiqueta::destroy($ids) > 0;
    }
}
