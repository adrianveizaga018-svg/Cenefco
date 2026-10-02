<?php

namespace App\Infrastructure\MdlCourses\Repositories;

use App\Application\MdlCourses\DTOs\MdlCourseDTO;
use App\Domain\MdlCourses\Contracts\MdlCourseRepositoryInterface;
use App\Domain\MdlCourses\Exceptions\MdlCourseNotFoundException;
use App\Infrastructure\MdlCourses\Models\MdlCourse;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentMdlCourseRepository implements MdlCourseRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUsReg, ?int $idDocente, bool $conInactivos): array
    {
        $q = MdlCourse::query();

        if (! $conInactivos) {
            $q->where('estado', 1);
        }
        if ($idUsReg !== null) {
            $q->where('id_us_reg', $idUsReg);
        }
        if ($idDocente !== null) {
            $q->where('id_docente', $idDocente);
        }

        $paginated = $q->orderByDesc('fecha_reg')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($r) => MdlCourseDTO::fromRow($r))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): object
    {
        $row = MdlCourse::find($id);
        if (! $row) {
            throw new MdlCourseNotFoundException($id);
        }
        return $row;
    }

    public function create(array $data): object
    {
        return MdlCourse::create($data);
    }

    public function update(int $id, array $data): void
    {
        $this->findById($id)->update($data);
    }

    public function delete(int $id): void
    {
        $this->findById($id)->update(['estado' => 0]);
    }
}
