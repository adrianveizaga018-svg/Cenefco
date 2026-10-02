<?php

namespace App\Infrastructure\CategoriasCampo\Repositories;

use App\Application\CategoriasCampo\DTOs\CategoriaCampoDTO;
use App\Domain\CategoriasCampo\Contracts\CategoriaCampoRepositoryInterface;
use App\Domain\CategoriasCampo\Exceptions\CategoriaCampoNotFoundException;
use App\Infrastructure\CategoriasCampo\Models\CategoriaCampo;
use Illuminate\Support\Facades\DB;

class EloquentCategoriaCampoRepository implements CategoriaCampoRepositoryInterface
{
    public function findByCategoria(int $categoriaId): array
    {
        return CategoriaCampo::where('categoria_id', $categoriaId)
            ->orderBy('orden')
            ->orderBy('id')
            ->get()
            ->map(fn ($m) => CategoriaCampoDTO::fromModel($m))
            ->all();
    }

    public function findById(int $id): CategoriaCampoDTO
    {
        $model = CategoriaCampo::find($id);
        if (! $model) {
            throw new CategoriaCampoNotFoundException($id);
        }

        return CategoriaCampoDTO::fromModel($model);
    }

    public function findByCategoriaAndId(int $categoriaId, int $id): CategoriaCampoDTO
    {
        $model = CategoriaCampo::find($id);
        if (! $model || (int) $model->categoria_id !== $categoriaId) {
            throw new CategoriaCampoNotFoundException($id);
        }

        return CategoriaCampoDTO::fromModel($model);
    }

    public function create(array $data): CategoriaCampoDTO
    {
        $model = CategoriaCampo::create($data);

        return CategoriaCampoDTO::fromModel($model);
    }

    public function update(int $id, array $data): CategoriaCampoDTO
    {
        $model = CategoriaCampo::find($id);
        if (! $model) {
            throw new CategoriaCampoNotFoundException($id);
        }
        $model->update($data);

        return CategoriaCampoDTO::fromModel($model->fresh());
    }

    public function delete(int $categoriaId, int $id): bool
    {
        $model = CategoriaCampo::find($id);
        if (! $model || (int) $model->categoria_id !== $categoriaId) {
            throw new CategoriaCampoNotFoundException($id);
        }

        return (bool) $model->delete();
    }

    public function reorder(int $categoriaId, array $items): void
    {
        DB::transaction(function () use ($categoriaId, $items) {
            foreach ($items as $item) {
                CategoriaCampo::where('id', (int) $item['id'])
                    ->where('categoria_id', $categoriaId)
                    ->update(['orden' => (int) $item['orden']]);
            }
        });
    }

    public function existeNombreCampo(int $categoriaId, string $nombreCampo, ?int $excludeId = null): bool
    {
        $q = CategoriaCampo::where('categoria_id', $categoriaId)
            ->where('nombre_campo', $nombreCampo);

        if ($excludeId !== null) {
            $q->where('id', '!=', $excludeId);
        }

        return $q->exists();
    }
}
