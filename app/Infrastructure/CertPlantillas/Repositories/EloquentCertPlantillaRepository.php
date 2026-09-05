<?php

namespace App\Infrastructure\CertPlantillas\Repositories;

use App\Application\CertPlantillas\DTOs\CertPlantillaDTO;
use App\Domain\CertPlantillas\Contracts\CertPlantillaRepositoryInterface;
use App\Domain\CertPlantillas\Exceptions\CertPlantillaNotFoundException;
use App\Infrastructure\CertPlantillas\Models\CertPlantilla;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentCertPlantillaRepository implements CertPlantillaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $tipo, bool $soloActivos): array
    {
        $q = CertPlantilla::query();

        if ($pagination->query) {
            $q->where('nombre', 'like', "%{$pagination->query}%");
        }
        if ($tipo !== null) {
            $q->where('tipo', $tipo);
        }
        if ($soloActivos) {
            $q->where('estado', 'activo');
        }

        $total = $q->count();
        $data  = $q->orderBy('nombre')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => CertPlantillaDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = CertPlantilla::find($id);
        if (! $row) {
            throw new CertPlantillaNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return CertPlantilla::create($data);
    }

    public function update(int $id, array $data): mixed
    {
        $row = CertPlantilla::find($id);
        if (! $row) {
            throw new CertPlantillaNotFoundException($id);
        }

        $row->fill($data)->save();

        return $row->fresh();
    }

    public function delete(int $id): bool
    {
        $deleted = CertPlantilla::where('id', $id)->delete();
        if (! $deleted) {
            throw new CertPlantillaNotFoundException($id);
        }

        return true;
    }
}
