<?php

namespace App\Infrastructure\Profesiones\Repositories;

use App\Application\Profesiones\DTOs\ProfesionDTO;
use App\Domain\Profesiones\Contracts\ProfesionRepositoryInterface;
use App\Domain\Profesiones\Exceptions\ProfesionNotFoundException;
use App\Infrastructure\Profesiones\Models\Profesion;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentProfesionRepository implements ProfesionRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, bool $soloActivos = false): array
    {
        $q = Profesion::query();

        if ($query) {
            $q->where('nombre', 'like', "%{$query}%");
        }

        if ($soloActivos) {
            $q->where('activo', true);
        }

        $total = $q->count();
        $data  = (clone $q)->orderBy('orden')->orderBy('nombre')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => ProfesionDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = Profesion::find($id);
        if (! $row) {
            throw new ProfesionNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return Profesion::create($data);
    }

    public function update(int $id, array $data): void
    {
        Profesion::where('id', $id)->update($data);
    }

    public function delete(int $id): void
    {
        Profesion::where('id', $id)->delete();
    }
}
