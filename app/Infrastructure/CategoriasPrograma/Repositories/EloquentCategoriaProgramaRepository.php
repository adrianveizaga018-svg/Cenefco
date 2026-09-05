<?php

namespace App\Infrastructure\CategoriasPrograma\Repositories;

use App\Application\CategoriasPrograma\DTOs\CategoriaProgramaDTO;
use App\Domain\CategoriasPrograma\Contracts\CategoriaProgramaRepositoryInterface;
use App\Domain\CategoriasPrograma\Exceptions\CategoriaProgramaNotFoundException;
use App\Infrastructure\CategoriasPrograma\Models\CategoriaPrograma;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentCategoriaProgramaRepository implements CategoriaProgramaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array
    {
        $q = CategoriaPrograma::query();

        if ($soloActivos) {
            $q->where('activo', true);
        }

        if ($pagination->query) {
            $q->where(function ($builder) use ($pagination) {
                $builder->where('nombre', 'like', "%{$pagination->query}%")
                    ->orWhere('descripcion', 'like', "%{$pagination->query}%");
            });
        }

        $paginated = $q->orderBy('orden', 'asc')
            ->orderBy('nombre', 'asc')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => CategoriaProgramaDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): CategoriaProgramaDTO
    {
        $model = CategoriaPrograma::find($id);
        if (! $model) {
            throw new CategoriaProgramaNotFoundException($id);
        }

        return CategoriaProgramaDTO::fromModel($model);
    }

    public function findBySlug(string $slug): CategoriaProgramaDTO
    {
        $model = CategoriaPrograma::where('slug', $slug)->first();
        if (! $model) {
            throw new CategoriaProgramaNotFoundException($slug);
        }

        return CategoriaProgramaDTO::fromModel($model);
    }

    public function create(array $data): CategoriaProgramaDTO
    {
        $model = CategoriaPrograma::create($data);

        return CategoriaProgramaDTO::fromModel($model);
    }

    public function update(int $id, array $data): CategoriaProgramaDTO
    {
        $model = CategoriaPrograma::find($id);
        if (! $model) {
            throw new CategoriaProgramaNotFoundException($id);
        }
        $model->update($data);

        return CategoriaProgramaDTO::fromModel($model);
    }

    public function delete(int $id): bool
    {
        return CategoriaPrograma::destroy($id) > 0;
    }

    public function tiposLegacy(): array
    {
        return DB::table('t_tipoprograma')
            ->orderBy('id_tipoprograma')
            ->get(['id_tipoprograma', 'nombre_tipoprograma'])
            ->all();
    }

    public function indexComoTipos(): array
    {
        return CategoriaPrograma::where('activo', true)
            ->whereNotNull('tipo_programa_id')
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get(['tipo_programa_id as id_tipoprograma', 'nombre as nombre_tipoprograma', 'id'])
            ->all();
    }
}
