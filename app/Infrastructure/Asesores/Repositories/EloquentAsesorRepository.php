<?php

namespace App\Infrastructure\Asesores\Repositories;

use App\Application\Asesores\DTOs\AsesorDTO;
use App\Domain\Asesores\Contracts\AsesorRepositoryInterface;
use App\Domain\Asesores\Exceptions\AsesorNotFoundException;
use App\Infrastructure\Asesores\Models\Asesor;

class EloquentAsesorRepository implements AsesorRepositoryInterface
{
    public function paginate(int $pageIndex, int $pageSize, ?string $query, ?bool $activo): array
    {
        $q = Asesor::query();

        if ($query) {
            $q->where(function ($sub) use ($query) {
                $sub->where('nombre',   'like', "%{$query}%")
                    ->orWhere('telefono', 'like', "%{$query}%")
                    ->orWhere('email',    'like', "%{$query}%");
            });
        }
        if ($activo !== null) {
            $q->where('activo', $activo);
        }

        $total  = $q->count();
        $items  = (clone $q)
            ->orderBy('nombre')
            ->offset(($pageIndex - 1) * $pageSize)
            ->limit($pageSize)
            ->get();

        return [
            'data'  => $items->map(fn ($r) => AsesorDTO::fromRow($r))->all(),
            'total' => $total,
        ];
    }

    public function findById(int $id): object
    {
        $row = Asesor::find($id);
        if (! $row) {
            throw new AsesorNotFoundException($id);
        }
        return $row;
    }

    public function create(array $data): object
    {
        return Asesor::create($data);
    }

    public function update(int $id, array $data): void
    {
        $this->findById($id)->update($data);
    }

    public function delete(int $id): void
    {
        $row = $this->findById($id);
        $row->delete();
    }

    public function existsByTelefono(string $telefono, ?int $excludeId): bool
    {
        return Asesor::where('telefono', $telefono)
            ->when($excludeId !== null, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }
}
