<?php

namespace App\Infrastructure\Visitas\Repositories;

use App\Application\Visitas\DTOs\VisitaDTO;
use App\Domain\Visitas\Contracts\VisitaRepositoryInterface;
use App\Infrastructure\Visitas\Models\Visita;
use App\Shared\Kernel\Support\SqlCompat;
use Illuminate\Support\Facades\DB;

class EloquentVisitaRepository implements VisitaRepositoryInterface
{
    public function create(array $data): VisitaDTO
    {
        $model = Visita::create($data);
        return VisitaDTO::fromModel($model);
    }

    public function stats(string $desde, string $hasta): array
    {
        $base = Visita::query()
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta);

        $totalVisitas     = (clone $base)->count();
        $sesionesUnicas   = (clone $base)->distinct('session_id')->count('session_id');
        $duracionPromedio = (clone $base)->whereNotNull('duracion_seg')->avg('duracion_seg');
        $paginaTop        = (clone $base)
            ->select('ruta', DB::raw('COUNT(*) as visitas'))
            ->groupBy('ruta')
            ->orderByDesc('visitas')
            ->first();

        $topPaginas = (clone $base)
            ->select('ruta', DB::raw('COUNT(*) as visitas'))
            ->groupBy('ruta')
            ->orderByDesc('visitas')
            ->limit(10)
            ->get()
            ->map(fn ($r) => ['ruta' => $r->ruta, 'visitas' => (int) $r->visitas]);

        $porDia = (clone $base)
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('COUNT(*) as visitas'))
            ->groupByRaw('DATE(created_at)')
            ->orderBy('fecha')
            ->get()
            ->map(fn ($r) => ['fecha' => $r->fecha, 'visitas' => (int) $r->visitas]);

        $porHora = (clone $base)
            ->select(DB::raw(SqlCompat::hour('created_at') . ' as hora'), DB::raw('COUNT(*) as visitas'))
            ->groupByRaw(SqlCompat::hour('created_at'))
            ->orderBy('hora')
            ->get()
            ->map(fn ($r) => ['hora' => (int) $r->hora, 'visitas' => (int) $r->visitas]);

        $porPais = (clone $base)
            ->select('pais', DB::raw('COUNT(*) as visitas'))
            ->whereNotNull('pais')
            ->groupBy('pais')
            ->orderByDesc('visitas')
            ->limit(15)
            ->get()
            ->map(fn ($r) => ['pais' => $r->pais, 'visitas' => (int) $r->visitas]);

        $porDispositivo = (clone $base)
            ->select('dispositivo', DB::raw('COUNT(*) as visitas'))
            ->whereNotNull('dispositivo')
            ->groupBy('dispositivo')
            ->get()
            ->map(fn ($r) => ['dispositivo' => $r->dispositivo, 'visitas' => (int) $r->visitas]);

        $porNavegador = (clone $base)
            ->select('navegador', DB::raw('COUNT(*) as visitas'))
            ->whereNotNull('navegador')
            ->groupBy('navegador')
            ->orderByDesc('visitas')
            ->limit(8)
            ->get()
            ->map(fn ($r) => ['navegador' => $r->navegador, 'visitas' => (int) $r->visitas]);

        $porSO = (clone $base)
            ->select('so', DB::raw('COUNT(*) as visitas'))
            ->whereNotNull('so')
            ->groupBy('so')
            ->orderByDesc('visitas')
            ->limit(8)
            ->get()
            ->map(fn ($r) => ['so' => $r->so, 'visitas' => (int) $r->visitas]);

        return [
            'kpis' => [
                'total_visitas'     => $totalVisitas,
                'sesiones_unicas'   => $sesionesUnicas,
                'duracion_promedio' => $duracionPromedio ? (int) round((float) $duracionPromedio) : null,
                'pagina_top'        => $paginaTop?->ruta,
            ],
            'top_paginas'     => $topPaginas,
            'por_dia'         => $porDia,
            'por_hora'        => $porHora,
            'por_pais'        => $porPais,
            'por_dispositivo' => $porDispositivo,
            'por_navegador'   => $porNavegador,
            'por_so'          => $porSO,
        ];
    }
}
