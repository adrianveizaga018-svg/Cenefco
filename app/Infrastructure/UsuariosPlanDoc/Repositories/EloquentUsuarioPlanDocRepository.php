<?php

namespace App\Infrastructure\UsuariosPlanDoc\Repositories;

use App\Application\UsuariosPlanDoc\DTOs\UsuarioPlanDocDTO;
use App\Domain\UsuariosPlanDoc\Contracts\UsuarioPlanDocRepositoryInterface;
use App\Domain\UsuariosPlanDoc\Exceptions\UsuarioPlanDocNotFoundException;
use App\Infrastructure\UsuariosPlanDoc\Models\UsuarioPlanDoc;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentUsuarioPlanDocRepository implements UsuarioPlanDocRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?int $idPlanDoc, bool $conInactivos): array
    {
        $q = UsuarioPlanDoc::query();

        if ($idUs !== null) {
            $q->where('id_us', $idUs);
        }
        if ($idPlanDoc !== null) {
            $q->where('id_plandoc', $idPlanDoc);
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderBy('id_usuarioplandoc')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get();

        return [
            'data'  => $data->map(fn ($r) => UsuarioPlanDocDTO::fromRow($r))->all(),
            'total' => $total,
        ];
    }

    public function findById(int $id): mixed
    {
        $row = UsuarioPlanDoc::where('id_usuarioplandoc', $id)->first();
        if (! $row) {
            throw new UsuarioPlanDocNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return UsuarioPlanDoc::create($data);
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
