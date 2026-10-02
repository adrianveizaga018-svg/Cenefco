<?php

namespace App\Infrastructure\UsuariosMoodle\Repositories;

use App\Application\UsuariosMoodle\DTOs\UsuarioMoodleDTO;
use App\Domain\UsuariosMoodle\Contracts\UsuarioMoodleRepositoryInterface;
use App\Domain\UsuariosMoodle\Exceptions\UsuarioMoodleNotFoundException;
use App\Infrastructure\UsuariosMoodle\Models\UsuarioMoodle;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentUsuarioMoodleRepository implements UsuarioMoodleRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?int $idMoodle, bool $conInactivos): array
    {
        $q = UsuarioMoodle::query();

        if ($idUs !== null) {
            $q->where('id_us', $idUs);
        }
        if ($idMoodle !== null) {
            $q->where('id_moodle', $idMoodle);
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderBy('id_usmoodle')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get();

        return [
            'data'  => $data->map(fn ($r) => UsuarioMoodleDTO::fromRow($r))->all(),
            'total' => $total,
        ];
    }

    public function findById(int $id): mixed
    {
        $row = UsuarioMoodle::where('id_usmoodle', $id)->first();
        if (! $row) {
            throw new UsuarioMoodleNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return UsuarioMoodle::create($data);
    }

    public function update(int $id, array $data): mixed
    {
        $row = $this->findById($id);
        $row->update($data);

        return $row->fresh();
    }

    public function delete(int $id): void
    {
        $row = $this->findById($id);
        $row->update(['estado' => 0]);
    }
}
