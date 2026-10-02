<?php

namespace App\Infrastructure\Trivia\Repositories;

use App\Application\Trivia\DTOs\TriviaNivelDTO;
use App\Domain\Trivia\Contracts\TriviaNivelRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaNivelNotFoundException;
use App\Infrastructure\Trivia\Models\TriviaNivel;

class EloquentTriviaNivelRepository implements TriviaNivelRepositoryInterface
{
    public function findByCategoria(int $categoriaId, bool $soloActivos = false): array
    {
        $q = TriviaNivel::query()->where('categoria_id', $categoriaId);

        if ($soloActivos) {
            $q->where('activo', true);
        }

        return $q->orderBy('orden')
            ->get()
            ->map(fn ($m) => TriviaNivelDTO::fromModel($m))
            ->all();
    }

    public function findById(int $id): TriviaNivelDTO
    {
        $model = TriviaNivel::find($id);
        if (! $model) {
            throw new TriviaNivelNotFoundException($id);
        }

        return TriviaNivelDTO::fromModel($model);
    }

    public function create(array $data): TriviaNivelDTO
    {
        $model = TriviaNivel::create($data);

        return TriviaNivelDTO::fromModel($model);
    }

    public function update(int $id, array $data): TriviaNivelDTO
    {
        $model = TriviaNivel::find($id);
        if (! $model) {
            throw new TriviaNivelNotFoundException($id);
        }
        $model->update($data);

        return TriviaNivelDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return TriviaNivel::destroy($ids) > 0;
    }
}
