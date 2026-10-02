<?php

namespace App\Infrastructure\TiposPostgrado\Repositories;

use App\Application\TiposPostgrado\DTOs\TipoPostgradoDTO;
use App\Domain\TiposPostgrado\Contracts\TipoPostgradoRepositoryInterface;
use App\Domain\TiposPostgrado\Exceptions\TipoPostgradoNotFoundException;
use App\Infrastructure\TiposPostgrado\Models\TipoPostgrado;

class EloquentTipoPostgradoRepository implements TipoPostgradoRepositoryInterface
{
    public function paginate(int $pageIndex, int $pageSize, ?int $idPlan, ?int $idTipopago, bool $conInactivos): array
    {
        $q = TipoPostgrado::query();

        if ($idPlan !== null) {
            $q->where('id_plan', $idPlan);
        }
        if ($idTipopago !== null) {
            $q->where('id_tipopago', $idTipopago);
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = (clone $q)
            ->orderBy('id_tipopost')
            ->offset(($pageIndex - 1) * $pageSize)
            ->limit($pageSize)
            ->get();

        return [
            'data'  => $data->map(fn ($r) => TipoPostgradoDTO::fromRow($r))->all(),
            'total' => $total,
        ];
    }

    public function findById(int $id): object
    {
        $row = TipoPostgrado::find($id);
        if (! $row) {
            throw new TipoPostgradoNotFoundException($id);
        }
        return $row;
    }

    public function create(array $data): object
    {
        return TipoPostgrado::create($data);
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
