<?php

namespace App\Infrastructure\Secretarias\Repositories;

use App\Application\Secretarias\DTOs\SecretariaDTO;
use App\Domain\Secretarias\Contracts\SecretariaRepositoryInterface;
use App\Domain\Secretarias\Exceptions\SecretariaNotFoundException;
use App\Infrastructure\Secretarias\Models\Secretaria;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentSecretariaRepository implements SecretariaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array
    {
        $q = Secretaria::query();

        if ($soloActivos) {
            $q->where('activa', true);
        }

        if ($pagination->query) {
            if (\DB::connection()->getDriverName() === 'pgsql') {
                $q->whereRaw(
                    "to_tsvector('spanish', coalesce(nombre,'') || ' ' || coalesce(sigla,'') || ' ' || coalesce(atribuciones,'')) @@ plainto_tsquery('spanish', ?)",
                    [$pagination->query]
                );
            } else {
                $q->whereFullText(['nombre', 'sigla', 'atribuciones'], $pagination->query);
            }
        }

        $paginated = $q->orderBy($pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => SecretariaDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): SecretariaDTO
    {
        $model = Secretaria::find($id);
        if (! $model) {
            throw new SecretariaNotFoundException($id);
        }

        return SecretariaDTO::fromModel($model);
    }

    public function findBySlug(string $slug): SecretariaDTO
    {
        $model = Secretaria::where('slug', $slug)->first();
        if (! $model) {
            throw new SecretariaNotFoundException($slug);
        }

        return SecretariaDTO::fromModel($model);
    }

    public function create(array $data): SecretariaDTO
    {
        $model = Secretaria::create($data);

        return SecretariaDTO::fromModel($model);
    }

    public function update(int $id, array $data): SecretariaDTO
    {
        $model = Secretaria::find($id);
        if (! $model) {
            throw new SecretariaNotFoundException($id);
        }
        $model->update($data);

        return SecretariaDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return Secretaria::destroy($ids) > 0;
    }
}
