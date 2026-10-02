<?php

namespace App\Infrastructure\Materias\Repositories;

use App\Application\Materias\DTOs\MateriaDTO;
use App\Domain\Materias\Contracts\MateriaRepositoryInterface;
use App\Domain\Materias\Exceptions\MateriaNotFoundException;
use App\Infrastructure\Materias\Models\Materia;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentMateriaRepository implements MateriaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $conInactivos): array
    {
        $q = Materia::query();

        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        if ($pagination->query) {
            $search = $pagination->query;
            $q->where(fn ($sq) => $sq
                ->where('nombre', 'like', "%{$search}%")
                ->orWhere('sigla', 'like', "%{$search}%")
            );
        }

        $total = $q->count();
        $data  = $q->orderBy('nombre')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($m) => MateriaDTO::fromModel($m))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): MateriaDTO
    {
        $materia = $this->findModelById($id);
        if (! $materia) {
            throw new MateriaNotFoundException($id);
        }

        return MateriaDTO::fromModel($materia);
    }

    public function create(array $data): MateriaDTO
    {
        $materia = Materia::create($data);

        return MateriaDTO::fromModel($materia);
    }

    public function update(int $id, array $data): MateriaDTO
    {
        $materia = $this->findModelById($id);
        if (! $materia) {
            throw new MateriaNotFoundException($id);
        }

        $materia->update($data);

        return MateriaDTO::fromModel($materia);
    }

    public function delete(int $id): void
    {
        $materia = $this->findModelById($id);
        if (! $materia) {
            throw new MateriaNotFoundException($id);
        }

        $materia->update(['estado' => 0]);
    }

    private function findModelById(int $id): ?Materia
    {
        return Materia::where('id_mat', $id)->orderBy('id_us_reg')->first();
    }
}
