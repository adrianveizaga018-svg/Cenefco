<?php

namespace App\Infrastructure\CategoriasNoticia\Repositories;

use App\Application\CategoriasNoticia\DTOs\CategoriaNoticiaDTO;
use App\Domain\CategoriasNoticia\Contracts\CategoriaNoticiaRepositoryInterface;
use App\Domain\CategoriasNoticia\Exceptions\CategoriaNoticiaNotFoundException;
use App\Infrastructure\CategoriasNoticia\Models\CategoriaNoticia;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentCategoriaNoticiaRepository implements CategoriaNoticiaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = CategoriaNoticia::query();

        if ($pagination->query) {
            $q->where('nombre', 'like', "%{$pagination->query}%")
                ->orWhere('descripcion', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy($pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => CategoriaNoticiaDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): CategoriaNoticiaDTO
    {
        $model = CategoriaNoticia::find($id);
        if (! $model) {
            throw new CategoriaNoticiaNotFoundException($id);
        }

        return CategoriaNoticiaDTO::fromModel($model);
    }

    public function findBySlug(string $slug): CategoriaNoticiaDTO
    {
        $model = CategoriaNoticia::where('slug', $slug)->first();
        if (! $model) {
            throw new CategoriaNoticiaNotFoundException($slug);
        }

        return CategoriaNoticiaDTO::fromModel($model);
    }

    public function create(array $data): CategoriaNoticiaDTO
    {
        $model = CategoriaNoticia::create($data);

        return CategoriaNoticiaDTO::fromModel($model);
    }

    public function update(int $id, array $data): CategoriaNoticiaDTO
    {
        $model = CategoriaNoticia::find($id);
        if (! $model) {
            throw new CategoriaNoticiaNotFoundException($id);
        }
        $model->update($data);

        return CategoriaNoticiaDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return CategoriaNoticia::destroy($ids) > 0;
    }
}
