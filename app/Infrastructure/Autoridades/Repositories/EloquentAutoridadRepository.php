<?php

namespace App\Infrastructure\Autoridades\Repositories;

use App\Application\Autoridades\DTOs\AutoridadDTO;
use App\Domain\Autoridades\Contracts\AutoridadRepositoryInterface;
use App\Domain\Autoridades\Exceptions\AutoridadNotFoundException;
use App\Infrastructure\Autoridades\Models\Autoridad;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentAutoridadRepository implements AutoridadRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false, bool $soloPublicadas = false): array
    {
        $q = Autoridad::query();

        if ($soloActivos) {
            $q->where('activo', true);
        }

        if ($soloPublicadas) {
            $q->where('publicado_web', true);
        }

        if ($pagination->query) {
            $q->where('nombre_completo', 'like', "%{$pagination->query}%")
                ->orWhere('cargo', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy($pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => AutoridadDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): AutoridadDTO
    {
        $model = Autoridad::find($id);
        if (! $model) {
            throw new AutoridadNotFoundException($id);
        }

        return AutoridadDTO::fromModel($model);
    }

    public function findBySlug(string $slug): AutoridadDTO
    {
        $model = Autoridad::where('slug', $slug)->first();
        if (! $model) {
            throw new AutoridadNotFoundException($slug);
        }

        return AutoridadDTO::fromModel($model);
    }

    public function porTipo(string $tipo, bool $soloActivos = true, bool $soloPublicadas = false): array
    {
        $q = Autoridad::where('tipo', $tipo)->orderBy('orden');

        if ($soloActivos) {
            $q->where('activo', true);
        }

        if ($soloPublicadas) {
            $q->where('publicado_web', true);
        }

        return $q->get()->map(fn ($m) => AutoridadDTO::fromModel($m))->all();
    }

    public function create(array $data): AutoridadDTO
    {
        $model = Autoridad::create($data);

        return AutoridadDTO::fromModel($model);
    }

    public function update(int $id, array $data): AutoridadDTO
    {
        
        $model = Autoridad::find($id);
        if (! $model) {
            throw new AutoridadNotFoundException($id);
        }
        $model->update($data);

        return AutoridadDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return Autoridad::destroy($ids) > 0;
    }
}
