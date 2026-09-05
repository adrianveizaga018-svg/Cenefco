<?php

namespace App\Infrastructure\Trivia\Repositories;

use App\Application\Trivia\DTOs\TriviaCategoriaDTO;
use App\Domain\Trivia\Contracts\TriviaCategoriaRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaCategoriaNotFoundException;
use App\Infrastructure\Trivia\Models\TriviaCategoria;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentTriviaCategoriaRepository implements TriviaCategoriaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array
    {
        $q = TriviaCategoria::query();

        if ($soloActivos) {
            $q->where('activo', true);
        }

        if ($pagination->query) {
            $q->where('nombre', 'like', "%{$pagination->query}%")
                ->orWhere('descripcion', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy($pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => TriviaCategoriaDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): TriviaCategoriaDTO
    {
        $model = TriviaCategoria::find($id);
        if (! $model) {
            throw new TriviaCategoriaNotFoundException($id);
        }

        return TriviaCategoriaDTO::fromModel($model);
    }

    public function findBySlug(string $slug): TriviaCategoriaDTO
    {
        $model = TriviaCategoria::where('slug', $slug)->first();
        if (! $model) {
            throw new TriviaCategoriaNotFoundException($slug);
        }

        return TriviaCategoriaDTO::fromModel($model);
    }

    public function create(array $data): TriviaCategoriaDTO
    {
        $model = TriviaCategoria::create($data);

        return TriviaCategoriaDTO::fromModel($model);
    }

    public function update(int $id, array $data): TriviaCategoriaDTO
    {
        $model = TriviaCategoria::find($id);
        if (! $model) {
            throw new TriviaCategoriaNotFoundException($id);
        }
        $model->update($data);

        return TriviaCategoriaDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return TriviaCategoria::destroy($ids) > 0;
    }
}
