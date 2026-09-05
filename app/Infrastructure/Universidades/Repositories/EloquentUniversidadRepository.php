<?php

namespace App\Infrastructure\Universidades\Repositories;

use App\Application\Universidades\DTOs\UniversidadDTO;
use App\Domain\Universidades\Contracts\UniversidadRepositoryInterface;
use App\Domain\Universidades\Exceptions\UniversidadNotFoundException;
use App\Infrastructure\Universidades\Models\Universidad;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentUniversidadRepository implements UniversidadRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, ?int $idCiudad, ?int $idTipoUniversidad): array
    {
        $q = Universidad::query();

        if ($query) {
            $q->where('nombre_universidad', 'like', "%{$query}%");
        }
        if ($idCiudad !== null) {
            $q->where('id_ciudad', $idCiudad);
        }
        if ($idTipoUniversidad !== null) {
            $q->where('id_tipouniversidad', $idTipoUniversidad);
        }

        $total = $q->count();
        $data  = $q->orderBy('nombre_universidad')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => UniversidadDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = Universidad::find($id);
        if (! $row) {
            throw new UniversidadNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return Universidad::create($data);
    }

    public function update(int $id, array $data): void
    {
        Universidad::where('id_universidad', $id)->update($data);
    }

    public function delete(int $id): void
    {
        Universidad::where('id_universidad', $id)->delete();
    }
}
