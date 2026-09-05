<?php

namespace App\Infrastructure\UsuariosPlan\Repositories;

use App\Application\UsuariosPlan\DTOs\UsuarioPlanDTO;
use App\Domain\UsuariosPlan\Contracts\UsuarioPlanRepositoryInterface;
use App\Domain\UsuariosPlan\Exceptions\UsuarioPlanNotFoundException;
use App\Infrastructure\UsuariosPlan\Models\UsuarioPlan;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentUsuarioPlanRepository implements UsuarioPlanRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?int $idPlan, bool $conInactivos): array
    {
        $q = UsuarioPlan::query();

        if ($idUs !== null) {
            $q->where('id_us', $idUs);
        }
        if ($idPlan !== null) {
            $q->where('id_plan', $idPlan);
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderBy('id_usuarioplan')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get();

        return [
            'data'  => $data->map(fn ($r) => UsuarioPlanDTO::fromRow($r))->all(),
            'total' => $total,
        ];
    }

    public function findById(int $id): mixed
    {
        $row = UsuarioPlan::where('id_usuarioplan', $id)->first();
        if (! $row) {
            throw new UsuarioPlanNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return UsuarioPlan::create($data);
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
