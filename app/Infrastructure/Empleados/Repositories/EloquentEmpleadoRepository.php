<?php

namespace App\Infrastructure\Empleados\Repositories;

use App\Application\Empleados\DTOs\EmpleadoDTO;
use App\Domain\Empleados\Contracts\EmpleadoRepositoryInterface;
use App\Domain\Empleados\Exceptions\EmpleadoNotFoundException;
use App\Infrastructure\Empleados\Models\Empleado;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentEmpleadoRepository implements EmpleadoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = Empleado::query()->whereNull('deleted_at');

        if ($pagination->query) {
            $q->where('nombre_completo', 'like', "%{$pagination->query}%");
        }

        $sortKey   = $pagination->sortKey ?: 'nombre_completo';
        $sortOrder = $pagination->sortOrder ?: 'asc';

        $paginated = $q->orderBy($sortKey, $sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($e) => EmpleadoDTO::fromModel($e))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): EmpleadoDTO
    {
        $m = Empleado::whereNull('deleted_at')->find($id);
        if (! $m) {
            throw new EmpleadoNotFoundException($id);
        }

        return EmpleadoDTO::fromModel($m);
    }

    public function findAllActivos(): array
    {
        return Empleado::where('activo', true)
            ->whereNull('deleted_at')
            ->orderBy('nombre_completo')
            ->get()
            ->map(fn ($e) => EmpleadoDTO::fromModel($e))
            ->all();
    }

    public function create(array $data): EmpleadoDTO
    {
        $m = Empleado::create($data);

        return EmpleadoDTO::fromModel($m);
    }

    public function update(int $id, array $data): EmpleadoDTO
    {
        $m = Empleado::whereNull('deleted_at')->find($id);
        if (! $m) {
            throw new EmpleadoNotFoundException($id);
        }

        $m->update($data);

        return EmpleadoDTO::fromModel($m->fresh());
    }

    public function delete(int $id): bool
    {
        $m = Empleado::whereNull('deleted_at')->find($id);
        if (! $m) {
            throw new EmpleadoNotFoundException($id);
        }

        return (bool) $m->delete();
    }
}
