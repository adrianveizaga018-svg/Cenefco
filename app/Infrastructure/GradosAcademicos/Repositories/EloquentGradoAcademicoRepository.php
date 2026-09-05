<?php

namespace App\Infrastructure\GradosAcademicos\Repositories;

use App\Application\GradosAcademicos\DTOs\GradoAcademicoDTO;
use App\Domain\GradosAcademicos\Contracts\GradoAcademicoRepositoryInterface;
use App\Domain\GradosAcademicos\Exceptions\GradoAcademicoNotFoundException;
use App\Infrastructure\GradosAcademicos\Models\GradoAcademico;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentGradoAcademicoRepository implements GradoAcademicoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, bool $soloActivos = false): array
    {
        $q = GradoAcademico::query();

        if ($query) {
            $q->where(function ($sq) use ($query) {
                $sq->where('nombre', 'like', "%{$query}%")
                   ->orWhere('abreviatura', 'like', "%{$query}%");
            });
        }

        if ($soloActivos) {
            $q->where('activo', true);
        }

        $total = $q->count();
        $data  = (clone $q)->orderBy('orden')->orderBy('nombre')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => GradoAcademicoDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = GradoAcademico::find($id);
        if (! $row) {
            throw new GradoAcademicoNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return GradoAcademico::create($data);
    }

    public function update(int $id, array $data): void
    {
        GradoAcademico::where('id', $id)->update($data);
    }

    public function delete(int $id): void
    {
        GradoAcademico::where('id', $id)->delete();
    }
}
