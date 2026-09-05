<?php

namespace App\Infrastructure\EfectosEspeciales\Repositories;

use App\Application\EfectosEspeciales\DTOs\EfectoEspecialDTO;
use App\Domain\EfectosEspeciales\Contracts\EfectoEspecialRepositoryInterface;
use App\Domain\EfectosEspeciales\Exceptions\EfectoEspecialNotFoundException;
use App\Infrastructure\EfectosEspeciales\Models\EfectoEspecial;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentEfectoEspecialRepository implements EfectoEspecialRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = EfectoEspecial::query();

        if ($pagination->query) {
            $term = '%' . $pagination->query . '%';
            $q->where('nombre', 'like', $term)
              ->orWhere('tipo_efecto', 'like', $term);
        }

        $allowed  = ['id', 'nombre', 'fecha_inicio', 'fecha_fin', 'activo', 'created_at'];
        $sortKey  = in_array($pagination->sortKey, $allowed, true) ? $pagination->sortKey : 'fecha_inicio';

        $paginated = $q->orderBy($sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => EfectoEspecialDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): EfectoEspecialDTO
    {
        $model = EfectoEspecial::find($id);
        if (!$model) throw new EfectoEspecialNotFoundException($id);
        return EfectoEspecialDTO::fromModel($model);
    }

    public function getActivos(): array
    {
        $hoy = now()->toDateString();
        return EfectoEspecial::where('activo', true)
            ->where('fecha_inicio', '<=', $hoy)
            ->where('fecha_fin',    '>=', $hoy)
            ->get()
            ->map(fn ($m) => EfectoEspecialDTO::fromModel($m))
            ->values()
            ->all();
    }

    public function create(array $data): EfectoEspecialDTO
    {
        return EfectoEspecialDTO::fromModel(EfectoEspecial::create($data));
    }

    public function update(int $id, array $data): EfectoEspecialDTO
    {
        $model = EfectoEspecial::find($id);
        if (!$model) throw new EfectoEspecialNotFoundException($id);
        $model->update($data);
        return EfectoEspecialDTO::fromModel($model);
    }

    public function delete(int $id): bool
    {
        return EfectoEspecial::destroy($id) > 0;
    }
}
