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
            // Mostrar campañas que estaban activas durante el período (fecha_inicio <= fecha_hasta Y fecha_fin >= fecha_desde o es nula)
            $q->where('cp.fecha_inicio', '<=', $filtros['fecha_desde'] > ($filtros['fecha_hasta'] ?? $filtros['fecha_desde']) ? $filtros['fecha_desde'] : ($filtros['fecha_hasta'] ?? $filtros['fecha_desde']));
        }
        if (! empty($filtros['fecha_desde']) && ! empty($filtros['fecha_hasta'])) {
            // Campañas que se solapan con el rango: inicio <= hasta Y (fin >= desde O fin es null)
            $q->where('cp.fecha_inicio', '<=', $filtros['fecha_hasta'])
              ->where(function($sub) use ($filtros) {
                  $sub->whereNull('cp.fecha_fin')
                      ->orWhere('cp.fecha_fin', '>=', $filtros['fecha_desde']);
              });
        } elseif (! empty($filtros['fecha_desde'])) {
            $q->where(function($sub) use ($filtros) {
                $sub->whereNull('cp.fecha_fin')
                    ->orWhere('cp.fecha_fin', '>=', $filtros['fecha_desde']);
            });
        } elseif (! empty($filtros['fecha_hasta'])) {
            $q->where('cp.fecha_inicio', '<=', $filtros['fecha_hasta']);
        }
        if (! empty($filtros['cuenta_id'])) {
            $q->where('cp.cuenta_externa_id', $filtros['cuenta_id']);
        }

        $total = (clone $q)->count();

        $sortKey   = $pagination->sortKey ?: 'fecha_inicio';
        $sortOrder = $pagination->sortOrder ?: 'desc';

        // Orden de estado compatible con SQLite y MySQL: activa > pausada > planificada > finalizada > cancelada
        $q->orderByRaw("CASE cp.estado 
            WHEN 'activa' THEN 1 
            WHEN 'pausada' THEN 2 
            WHEN 'planificada' THEN 3 
            WHEN 'finalizada' THEN 4 
            WHEN 'cancelada' THEN 5 
            ELSE 6 END ASC");

        $items = $q->orderBy("cp.{$sortKey}", $sortOrder)
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get();

        $ids = $items->pluck('id')->all();
        $metricasPorCampana = [];
        if (! empty($ids)) {
            $metricas = CampanaMetrica::whereIn('campana_publicidad_id', $ids)
                ->orderByDesc('fecha_corte')
                ->orderByDesc('id')
                ->get();
            foreach ($metricas as $m) {
                $metricasPorCampana[$m->campana_publicidad_id][] = CampanaMetricaDTO::fromModel($m);
            }
        }

        // --- INICIO MAGIA EN VIVO ---
        // Si el usuario aplicó filtro de fecha, quiere ver lo gastado en ESA fecha, no el histórico.
        // Hacemos una consulta rápida a Meta Ads por cuenta para sobreescribir los datos al vuelo.
        if (!empty($filtros['fecha_desde']) || !empty($filtros['fecha_hasta'])) {
            try {
                $metaAds = app(\App\Infrastructure\Meta\MetaAdsService::class);
                
                // Si filtraron por una cuenta específica, solo consultamos esa. Si no, consultamos las que están en el .env
                $cuentas = !empty($filtros['cuenta_id']) 
                    ? [$filtros['cuenta_id']] 
                    : explode(',', env('META_AD_ACCOUNT_IDS', '1652384699141172,1420958161864793'));

                $liveData = [];
                $liveStatuses = [];
                foreach ($cuentas as $cuentaId) {
                    $res = $metaAds->getAccountLiveInsights($cuentaId, $filtros['fecha_desde'] ?? null, $filtros['fecha_hasta'] ?? null);
                    $stats = $metaAds->getAccountCampaignStatuses($cuentaId);
                    
                    if ($res) {
                        $liveData = $liveData + $res; 
                    }
                    if ($stats) {
                        $liveStatuses = $liveStatuses + $stats;
                    }
                }

                $tipoCambio = env('TIPO_CAMBIO_USD_BOB', 6.96);

                // Sobreescribir las propiedades de $items con lo que dijo Meta en vivo
                foreach ($items as $item) {
                    if ($item->id_campana_externa && isset($liveStatuses[$item->id_campana_externa])) {
                        $item->estado = $liveStatuses[$item->id_campana_externa];
                    }

                    if ($item->id_campana_externa && isset($liveData[$item->id_campana_externa])) {
                        $live = $liveData[$item->id_campana_externa];
                        $item->presupuesto_usd = $live['spend'];
                        $item->presupuesto_bob = $live['spend'] * $tipoCambio;
                        $item->leads = $live['leads'];
                        // También inyectamos las métricas al vuelo si existen
                        $mDto = new CampanaMetricaDTO(
                            id: 0,
                            campana_publicidad_id: $item->id,
                            fecha_corte: date('Y-m-d'),
                            alcance: $live['reach'],
                            impresiones: $live['impressions'],
                            frecuencia: null,
                            clics_enlace: $live['clicks'],
                            ctr: null,
                            cpc: null,
                            cpm: null,
                            resultados: $live['leads'],
                            tipo_resultado: 'live',
                            costo_por_resultado: null,
                            gasto_periodo: $live['spend'] * $tipoCambio,
                            fuente: 'api_meta_live',
                            notas: null,
                            created_at: null
                        );
                        $metricasPorCampana[$item->id] = [$mDto];
                    } else if ($item->id_campana_externa && in_array($item->plataforma, ['meta_ads', 'facebook'])) {
                        // Si es de Meta pero no vino en $liveData, significa que no gastó NADA en ese rango de fechas
                        $item->presupuesto_usd = 0;
                        $item->presupuesto_bob = 0;
                        $item->leads = null;
                        
                        $mDto = new CampanaMetricaDTO(
                            id: 0,
                            campana_publicidad_id: $item->id,
                            fecha_corte: date('Y-m-d'),
                            alcance: 0,
                            impresiones: 0,
                            frecuencia: null,
                            clics_enlace: 0,
                            ctr: null,
                            cpc: null,
                            cpm: null,
                            resultados: 0,
                            tipo_resultado: 'live',
                            costo_por_resultado: null,
                            gasto_periodo: 0,
                            fuente: 'api_meta_live',
                            notas: null,
                            created_at: null
                        );
                        $metricasPorCampana[$item->id] = [$mDto];
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Fallo al obtener datos en vivo', ['err' => $e->getMessage()]);
            }
        }
        // --- FIN MAGIA EN VIVO ---

        return [
            'data'  => $items->map(function ($r) use ($metricasPorCampana) {
                $dto = CampanaPublicidadDTO::fromRow($r);
                if (isset($metricasPorCampana[$r->id])) {
                    return $dto->withMetricas($metricasPorCampana[$r->id]);
                }
                return $dto;
            })->all(),
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
                // Inscritos automáticos: conta inscripciones en ese programa desde que inició la campaña
                DB::raw('(
                    SELECT COUNT(DISTINCT i.id_ins)
                    FROM t_inscripcion i
                    WHERE i.id_imp = (
                        SELECT p3.id_imp FROM t_programa p3 WHERE p3.id_programa = cp.programa_id LIMIT 1
                    )
                    AND cp.programa_id IS NOT NULL
                    AND i.fecha_ins >= cp.fecha_inicio
                ) as inscritos_auto'),
            );
    }
}
