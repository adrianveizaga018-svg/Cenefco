<?php

namespace App\Infrastructure\Gastos\Repositories;

use App\Application\Gastos\DTOs\GastoDTO;
use App\Domain\Gastos\Contracts\GastoRepositoryInterface;
use App\Domain\Gastos\Exceptions\GastoNotFoundException;
use App\Infrastructure\Gastos\Models\Gasto;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentGastoRepository implements GastoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filtros = []): array
    {
        $q = Gasto::query()->with(['categoria', 'campanaPublicidad'])->whereNull('deleted_at');

        if ($pagination->query) {
            $q->where('concepto', 'like', "%{$pagination->query}%");
        }

        if (! empty($filtros['categoria_gasto_id'])) {
            $q->where('categoria_gasto_id', $filtros['categoria_gasto_id']);
        }

        if (! empty($filtros['campana_publicidad_id'])) {
            $q->where('campana_publicidad_id', $filtros['campana_publicidad_id']);
        }

        if (! empty($filtros['fecha_desde'])) {
            $q->whereDate('fecha', '>=', $filtros['fecha_desde']);
        }

        if (! empty($filtros['fecha_hasta'])) {
            $q->whereDate('fecha', '<=', $filtros['fecha_hasta']);
        }

        $sortKey   = $pagination->sortKey ?: 'fecha';
        $sortOrder = $pagination->sortOrder ?: 'desc';

        $paginated = $q->orderBy($sortKey, $sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($g) => GastoDTO::fromModel($g))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): GastoDTO
    {
        $m = Gasto::with(['categoria', 'campanaPublicidad'])->whereNull('deleted_at')->find($id);
        if (! $m) {
            throw new GastoNotFoundException($id);
        }

        return GastoDTO::fromModel($m);
    }

    public function create(array $data): GastoDTO
    {
        $m = Gasto::create($data);

        return GastoDTO::fromModel($m->load(['categoria', 'campanaPublicidad']));
    }

    public function update(int $id, array $data): GastoDTO
    {
        $m = Gasto::whereNull('deleted_at')->find($id);
        if (! $m) {
            throw new GastoNotFoundException($id);
        }

        $m->update($data);

        return GastoDTO::fromModel($m->fresh(['categoria', 'campanaPublicidad']));
    }

    public function delete(int $id): bool
    {
        $m = Gasto::whereNull('deleted_at')->find($id);
        if (! $m) {
            throw new GastoNotFoundException($id);
        }

        return (bool) $m->delete();
    }

    public function resumenMes(int $anio, int $mes): array
    {
        $total = Gasto::whereNull('deleted_at')
            ->whereYear('fecha', $anio)
            ->whereMonth('fecha', $mes)
            ->sum('monto');

        $porCategoria = Gasto::whereNull('deleted_at')
            ->whereYear('fecha', $anio)
            ->whereMonth('fecha', $mes)
            ->selectRaw('categoria_gasto_id, sum(monto) as total')
            ->groupBy('categoria_gasto_id')
            ->with('categoria:id,nombre')
            ->get()
            ->map(fn ($r) => [
                'categoria_id'     => $r->categoria_gasto_id,
                'categoria_nombre' => $r->categoria->nombre ?? null,
                'total'            => (float) $r->total,
            ])
            ->all();

        return [
            'total_mes'     => (float) $total,
            'por_categoria' => $porCategoria,
        ];
    }

    public function resumenPorLineaNegocio(int $anio, int $mes): array
    {
        return Gasto::whereNull('gasto.deleted_at')
            ->whereYear('fecha', $anio)
            ->whereMonth('fecha', $mes)
            ->join('categoria_gasto', 'categoria_gasto.id', '=', 'gasto.categoria_gasto_id')
            ->selectRaw('categoria_gasto.linea_negocio, sum(gasto.monto) as total')
            ->groupBy('categoria_gasto.linea_negocio')
            ->get()
            ->map(fn ($r) => [
                'linea_negocio' => $r->linea_negocio,
                'total'         => (float) $r->total,
            ])
            ->all();
    }
}
