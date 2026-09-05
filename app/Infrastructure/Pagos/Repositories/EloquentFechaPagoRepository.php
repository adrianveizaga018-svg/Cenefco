<?php

namespace App\Infrastructure\Pagos\Repositories;

use App\Application\Pagos\DTOs\FechaPagoDTO;
use App\Domain\Pagos\Contracts\FechaPagoRepositoryInterface;
use App\Infrastructure\Pagos\Models\FechaPago;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentFechaPagoRepository implements FechaPagoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filters = []): array
    {
        $q = FechaPago::query()
            ->leftJoin('t_plan as pl', 't_fechapago.id_plan', '=', 'pl.id_plan')
            ->select(['t_fechapago.*', 'pl.titulo as plan_titulo']);

        if (! empty($filters['id_plan'])) {
            $q->where('t_fechapago.id_plan', (int) $filters['id_plan']);
        }
        if (! empty($filters['tipo_tramite'])) {
            $q->where('t_fechapago.tipo_tramite', $filters['tipo_tramite']);
        }
        if (! isset($filters['conInactivos']) || ! $filters['conInactivos']) {
            $q->where('t_fechapago.estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderBy('t_fechapago.id_fechapago')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn($m) => FechaPagoDTO::fromModel($m))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        return FechaPago::find($id);
    }

    public function findByPlan(int $idPlan): array
    {
        return FechaPago::where('id_plan', $idPlan)
            ->where('estado', 1)
            ->orderBy('id_fechapago')
            ->get()
            ->map(fn($m) => FechaPagoDTO::fromModel($m))
            ->all();
    }

    public function create(array $data): mixed
    {
        return FechaPago::create($data);
    }

    public function update(int $id, array $data): mixed
    {
        $fp = FechaPago::findOrFail($id);
        $fp->update($data);
        return $fp->fresh();
    }

    public function anular(int $id): void
    {
        FechaPago::where('id_fechapago', $id)->update(['estado' => 0]);
    }
}
