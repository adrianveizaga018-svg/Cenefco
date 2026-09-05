<?php

namespace App\Infrastructure\AjustesSueldo\Repositories;

use App\Application\AjustesSueldo\DTOs\AjusteSueldoDTO;
use App\Domain\AjustesSueldo\Contracts\AjusteSueldoRepositoryInterface;
use App\Infrastructure\AjustesSueldo\Models\AjusteSueldo;

class EloquentAjusteSueldoRepository implements AjusteSueldoRepositoryInterface
{
    public function paginate(array $filters): array
    {
        $pagination = $filters['pagination'];

        $q = AjusteSueldo::query()->with('empleado')->orderByDesc('created_at');

        if (! empty($filters['empleado_id'])) {
            $q->where('empleado_id', $filters['empleado_id']);
        }
        if (! empty($filters['anio'])) {
            $q->where('anio', $filters['anio']);
        }
        if (! empty($filters['mes'])) {
            $q->where('mes', $filters['mes']);
        }
        if (array_key_exists('aplicado', $filters) && $filters['aplicado'] !== null) {
            $q->where('aplicado', $filters['aplicado']);
        }

        $paginated = $q->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => AjusteSueldoDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): mixed
    {
        return AjusteSueldo::with('empleado')->find($id);
    }

    public function pendientesDelPeriodo(int $empleadoId, int $anio, int $mes): array
    {
        return AjusteSueldo::with('empleado')
            ->where('empleado_id', $empleadoId)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->where('aplicado', false)
            ->orderBy('created_at')
            ->get()
            ->all();
    }

    public function create(array $data): mixed
    {
        return AjusteSueldo::create($data);
    }

    public function delete(int $id): bool
    {
        $m = AjusteSueldo::find($id);
        return $m ? (bool) $m->delete() : false;
    }

    public function marcarAplicados(array $ids, int $planillaDetalleId): void
    {
        if (empty($ids)) {
            return;
        }

        AjusteSueldo::whereIn('id', $ids)->update([
            'aplicado'             => true,
            'planilla_detalle_id'  => $planillaDetalleId,
        ]);
    }

    public function desaplicarPorPlanillaDetalle(array $planillaDetalleIds): void
    {
        if (empty($planillaDetalleIds)) {
            return;
        }

        AjusteSueldo::whereIn('planilla_detalle_id', $planillaDetalleIds)->update([
            'aplicado'            => false,
            'planilla_detalle_id' => null,
        ]);
    }
}
