<?php

namespace App\Infrastructure\TiposEvento\Repositories;

use App\Application\TiposEvento\DTOs\TipoEventoDTO;
use App\Domain\TiposEvento\Contracts\TipoEventoRepositoryInterface;
use App\Domain\TiposEvento\Exceptions\TipoEventoNotFoundException;
use App\Infrastructure\TiposEvento\Models\TipoEvento;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentTipoEventoRepository implements TipoEventoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = TipoEvento::query();

        if ($pagination->query) {
            $q->where('nombre', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy($pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => TipoEventoDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): TipoEventoDTO
    {
        $model = TipoEvento::find($id);
        if (! $model) {
            throw new TipoEventoNotFoundException($id);
        }

        return TipoEventoDTO::fromModel($model);
    }

    public function findBySlug(string $slug): TipoEventoDTO
    {
        $model = TipoEvento::where('slug', $slug)->first();
        if (! $model) {
            throw new TipoEventoNotFoundException($slug);
        }

        return TipoEventoDTO::fromModel($model);
    }

    public function create(array $data): TipoEventoDTO
    {
        $model = TipoEvento::create($data);

        return TipoEventoDTO::fromModel($model);
    }

    public function update(int $id, array $data): TipoEventoDTO
    {
        $model = TipoEvento::find($id);
        if (! $model) {
            throw new TipoEventoNotFoundException($id);
        }
        $model->update($data);

        return TipoEventoDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return TipoEvento::destroy($ids) > 0;
    }
}
