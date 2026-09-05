<?php

namespace App\Infrastructure\TiposNorma\Repositories;

use App\Application\TiposNorma\DTOs\TipoNormaDTO;
use App\Domain\TiposNorma\Contracts\TipoNormaRepositoryInterface;
use App\Domain\TiposNorma\Exceptions\TipoNormaNotFoundException;
use App\Infrastructure\TiposNorma\Models\TipoNorma;

class EloquentTipoNormaRepository implements TipoNormaRepositoryInterface
{
    public function all(): array
    {
        return TipoNorma::where('activo', true)
            ->orderBy('nombre')
            ->get()
            ->map(fn ($m) => TipoNormaDTO::fromModel($m))
            ->all();
    }

    public function findById(int $id): TipoNormaDTO
    {
        $model = TipoNorma::find($id);
        if (! $model) {
            throw new TipoNormaNotFoundException($id);
        }

        return TipoNormaDTO::fromModel($model);
    }

    public function findBySlug(string $slug): TipoNormaDTO
    {
        $model = TipoNorma::where('slug', $slug)->first();
        if (! $model) {
            throw new TipoNormaNotFoundException($slug);
        }

        return TipoNormaDTO::fromModel($model);
    }

    public function create(array $data): TipoNormaDTO
    {
        $model = TipoNorma::create($data);

        return TipoNormaDTO::fromModel($model);
    }

    public function update(int $id, array $data): TipoNormaDTO
    {
        
        $model = TipoNorma::find($id);
        if (! $model) {
            throw new TipoNormaNotFoundException($id);
        }
        $model->update($data);

        return TipoNormaDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return TipoNorma::destroy($ids) > 0;
    }
}
