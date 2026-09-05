<?php

namespace App\Infrastructure\CampanasPublicidad\Repositories;

use App\Application\CampanasPublicidad\DTOs\CampanaMetricaDTO;
use App\Application\CampanasPublicidad\DTOs\CampanaPublicidadDTO;
use App\Application\CampanasPublicidad\DTOs\ReporteCampanaDTO;
use App\Domain\CampanasPublicidad\Contracts\CampanaPublicidadRepositoryInterface;
use App\Domain\CampanasPublicidad\Exceptions\CampanaPublicidadNotFoundException;
use App\Infrastructure\CampanasPublicidad\Models\CampanaMetrica;
use App\Infrastructure\CampanasPublicidad\Models\CampanaPublicidad;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentCampanaPublicidadRepository implements CampanaPublicidadRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filtros = []): array
    {
        $q = $this->baseQuery();

        if ($pagination->query) {
            $q->where('cp.nombre', 'like', "%{$pagination->query}%");
        }
        if (! empty($filtros['programa_id'])) {
            $q->where('cp.programa_id', $filtros['programa_id']);
        }
        if (! empty($filtros['plataforma'])) {
            $q->where('cp.plataforma', $filtros['plataforma']);
        }
        if (! empty($filtros['estado'])) {
            $q->where('cp.estado', $filtros['estado']);
        }
        if (! empty($filtros['fecha_desde'])) {
            $q->where('cp.fecha_inicio', '>=', $filtros['fecha_desde']);
        }
        if (! empty($filtros['fecha_hasta'])) {
            $q->where('cp.fecha_inicio', '<=', $filtros['fecha_hasta']);
        }

        $total = (clone $q)->count();

        $sortKey   = $pagination->sortKey ?: 'fecha_inicio';
        $sortOrder = $pagination->sortOrder ?: 'desc';

        $items = $q->orderBy("cp.{$sortKey}", $sortOrder)
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get();

        return [
            'data'  => $items->map(fn ($r) => CampanaPublicidadDTO::fromRow($r))->all(),
            'total' => $total,
        ];
    }

    public function findById(int $id): CampanaPublicidadDTO
    {
        $row = $this->baseQuery()->where('cp.id', $id)->first();

        if (! $row) {
            throw new CampanaPublicidadNotFoundException($id);
        }

        $metricas = CampanaMetrica::where('campana_publicidad_id', $id)
            ->orderByDesc('fecha_corte')
            ->get()
            ->map(fn ($m) => CampanaMetricaDTO::fromModel($m))
            ->all();

        return CampanaPublicidadDTO::fromRow($row)->withMetricas($metricas);
    }

    public function create(array $data): CampanaPublicidadDTO
    {
        $model = CampanaPublicidad::create($data);

        return $this->findById($model->id);
    }

    public function update(int $id, array $data): CampanaPublicidadDTO
    {
        $model = CampanaPublicidad::find($id);
        if (! $model) {
            throw new CampanaPublicidadNotFoundException($id);
        }

        $model->update($data);

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $model = CampanaPublicidad::find($id);
        if (! $model) {
            throw new CampanaPublicidadNotFoundException($id);
        }

        return (bool) $model->delete();
    }

    public function tieneGastos(int $id): bool
    {
        return DB::table('gasto')
            ->where('campana_publicidad_id', $id)
            ->whereNull('deleted_at')
            ->exists();
    }

    public function registrarMetrica(int $campanaId, array $data): CampanaMetricaDTO
    {
        if (! CampanaPublicidad::whereNull('deleted_at')->where('id', $campanaId)->exists()) {
            throw new CampanaPublicidadNotFoundException($campanaId);
        }

        $metrica = CampanaMetrica::create(array_merge($data, [
            'campana_publicidad_id' => $campanaId,
        ]));

        return CampanaMetricaDTO::fromModel($metrica);
    }

    public function reportePorCurso(?string $fechaInicio, ?string $fechaFin): array
    {
        $q = DB::table('campana_publicidad as cp')
            ->whereNull('cp.deleted_at')
            ->select(
                'cp.id',
                'cp.programa_id',
                DB::raw('(SELECT COALESCE(SUM(monto), 0) FROM gasto g WHERE g.campana_publicidad_id = cp.id AND g.deleted_at IS NULL) as gasto_total'),
                DB::raw('(SELECT alcance FROM campana_metrica m WHERE m.campana_publicidad_id = cp.id ORDER BY m.fecha_corte DESC, m.id DESC LIMIT 1) as ultimo_alcance'),
                DB::raw('(SELECT resultados FROM campana_metrica m WHERE m.campana_publicidad_id = cp.id ORDER BY m.fecha_corte DESC, m.id DESC LIMIT 1) as ultimo_resultados'),
            );

        if ($fechaInicio) {
            $q->where('cp.fecha_inicio', '>=', $fechaInicio);
        }
        if ($fechaFin) {
            $q->where('cp.fecha_inicio', '<=', $fechaFin);
        }

        $campanas = $q->get();

        $porPrograma = $campanas->groupBy(fn ($c) => $c->programa_id ?? 'institucional');

        $programaIds = $campanas->pluck('programa_id')->filter()->unique()->values()->all();

        $programasInfo = collect();
        if (! empty($programaIds)) {
            $programasInfo = DB::table('t_programa as p')
                ->whereIn('p.id_programa', $programaIds)
                ->groupBy('p.id_programa')
                ->selectRaw('p.id_programa, MIN(p.nombre_programa) as nombre_programa, MIN(p.id_imp) as id_imp')
                ->get()
                ->keyBy('id_programa');
        }

        $idImps = $programasInfo->pluck('id_imp')->filter()->unique()->values()->all();

        $inscritosPorImp = collect();
        $recaudadoPorImp = collect();
        if (! empty($idImps)) {
            $inscritosPorImp = DB::table('t_inscripcion')
                ->whereIn('id_imp', $idImps)
                ->selectRaw('id_imp, COUNT(DISTINCT id_ins) as total')
                ->groupBy('id_imp')
                ->pluck('total', 'id_imp');

            $recaudadoPorImp = DB::table('t_inscripcion as i')
                ->join('t_pago as pg', 'pg.id_ins', '=', 'i.id_ins')
                ->whereIn('i.id_imp', $idImps)
                ->where('pg.estado', 1)
                ->selectRaw('i.id_imp, COALESCE(SUM(CAST(pg.monto_pagado AS DECIMAL(12,2))), 0) as total')
                ->groupBy('i.id_imp')
                ->pluck('total', 'id_imp');
        }

        $resultado = [];
        foreach ($porPrograma as $programaId => $grupo) {
            $programaIdInt = is_numeric($programaId) ? (int) $programaId : null;
            $info  = $programaIdInt ? $programasInfo->get($programaIdInt) : null;
            $idImp = $info->id_imp ?? null;

            $resultado[] = ReporteCampanaDTO::fromRow((object) [
                'programa_id'           => $programaIdInt,
                'programa_nombre'       => $info->nombre_programa ?? null,
                'total_invertido'       => $grupo->sum('gasto_total'),
                'total_alcance'         => $grupo->sum('ultimo_alcance'),
                'total_resultados'      => $grupo->sum('ultimo_resultados'),
                'total_inscritos_curso' => $idImp ? ($inscritosPorImp[$idImp] ?? 0) : null,
                'total_recaudado_curso' => $idImp ? (float) ($recaudadoPorImp[$idImp] ?? 0) : null,
            ]);
        }

        return $resultado;
    }

    private function baseQuery(): \Illuminate\Database\Query\Builder
    {
        return DB::table('campana_publicidad as cp')
            ->whereNull('cp.deleted_at')
            ->select(
                'cp.*',
                DB::raw('(SELECT p2.nombre_programa FROM t_programa p2 WHERE p2.id_programa = cp.programa_id ORDER BY p2.id_us_reg LIMIT 1) as programa_nombre'),
                DB::raw('(SELECT COALESCE(SUM(monto), 0) FROM gasto g WHERE g.campana_publicidad_id = cp.id AND g.deleted_at IS NULL) as total_gastado'),
            );
    }
}
