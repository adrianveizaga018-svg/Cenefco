<?php

namespace App\Application\Gastos\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Gastos que el sistema registra solo, para que el balance sea real sin cargar nada dos veces:
 *  - cada pago a un docente (Sueldos Docentes / Honorarios del mes),
 *  - lo gastado en Facebook por campaña y por mes (según la sincronización con Meta).
 * Cada gasto guarda su origen: se actualiza o anula junto con lo que lo originó.
 */
class GastosAutomaticosService
{
    public const HONORARIO = 'honorario_docente';
    public const PUBLICIDAD = 'publicidad_meta';

    private const CATEGORIA_HONORARIOS = 'Honorarios docentes';
    private const CATEGORIA_PUBLICIDAD = 'Publicidad en redes';

    // ── Pagos a docentes ────────────────────────────────────────────────────

    public function alPagarDocente(int $idPago): void
    {
        $p = DB::table('web_pago_sueldo as pg')
            ->join('web_sueldo_docente as s', 's.id', '=', 'pg.id_sueldo')
            ->leftJoin('t_imparte as imp', 'imp.id_imp', '=', 's.id_imp')
            ->leftJoin('web_docente_perfil as dp', function ($j) {
                // Los docentes sin cuenta se guardan con id negativo (-id del perfil)
                $j->on('dp.usuario_id', '=', 's.id_us')->orOn('dp.id', '=', DB::raw('-s.id_us'));
            })
            ->where('pg.id', $idPago)
            ->first(['pg.id', 'pg.monto_pagado', 'pg.fecha_pago', 'pg.comprobante_archivo', 'pg.estado',
                's.concepto', 's.id_programa', 'imp.id_mat', 'dp.nombre_completo']);

        if (! $p || (int) $p->estado !== 1) {
            $this->alAnularPagoDocente($idPago);

            return;
        }

        $docente = $p->nombre_completo ? " — {$p->nombre_completo}" : '';
        $this->guardar(self::HONORARIO, (string) $p->id, [
            'categoria_gasto_id' => $this->categoria(self::CATEGORIA_HONORARIOS),
            'concepto'           => mb_substr(($p->concepto ?: 'Honorario docente').$docente, 0, 255),
            'monto'              => round((float) $p->monto_pagado, 2),
            'fecha'              => Carbon::parse($p->fecha_pago)->toDateString(),
            'comprobante_url'    => $p->comprobante_archivo,
            'id_programa'        => $p->id_programa ?: $p->id_mat,
        ]);
    }

    public function alAnularPagoDocente(int $idPago): void
    {
        DB::table('gasto')->where('origen', self::HONORARIO)->where('origen_ref', (string) $idPago)
            ->whereNull('deleted_at')->update(['deleted_at' => now(), 'updated_at' => now()]);
    }

    // ── Publicidad de Facebook ──────────────────────────────────────────────

    /**
     * Un gasto por campaña y por mes, con lo que Facebook informó como gastado ese mes.
     * Meta guarda el gasto acumulado: el del mes es la diferencia entre el último corte del mes y el anterior.
     */
    public function sincronizarPublicidad(Carbon $desde, ?Carbon $hasta = null): int
    {
        $hasta ??= now();
        $categoria = $this->categoria(self::CATEGORIA_PUBLICIDAD);
        $tocados = 0;

        $campanas = DB::table('campana_publicidad')
            ->whereIn('id', DB::table('campana_metrica')->select('campana_publicidad_id'))
            ->get(['id', 'nombre', 'programa_id', 'fecha_inicio']);
        $cortes = DB::table('campana_metrica')->whereIn('campana_publicidad_id', $campanas->pluck('id'))
            ->orderBy('fecha_corte')->get(['campana_publicidad_id', 'fecha_corte', 'gasto_periodo'])
            ->groupBy('campana_publicidad_id');

        for ($mes = $desde->copy()->startOfMonth(); $mes->lte($hasta); $mes->addMonth()) {
            $inicio = $mes->copy()->subDay();
            $fin = $mes->copy()->endOfMonth();

            foreach ($campanas as $c) {
                $puntos = $this->puntos($c, $cortes[$c->id] ?? collect());
                $delMes = round(max(0.0, $this->acumuladoAl($puntos, $fin) - $this->acumuladoAl($puntos, $inicio)), 2);
                $ref = "{$c->id}:{$mes->format('Y-m')}";

                if ($delMes <= 0) {
                    $tocados += DB::table('gasto')->where('origen', self::PUBLICIDAD)->where('origen_ref', $ref)
                        ->whereNull('deleted_at')->update(['deleted_at' => now(), 'updated_at' => now()]);
                    continue;
                }

                $this->guardar(self::PUBLICIDAD, $ref, [
                    'categoria_gasto_id'    => $categoria,
                    'concepto'              => mb_substr("Facebook / Instagram — {$c->nombre}", 0, 255),
                    'monto'                 => $delMes,
                    'fecha'                 => min($fin->toDateString(), now()->toDateString()),
                    'campana_publicidad_id' => $c->id,
                    'id_programa'           => $c->programa_id,
                    'nota'                  => 'Registrado automáticamente con lo que informa Meta Ads. Si la campaña corrió '
                        .'entre dos lecturas de Facebook (o antes de la primera), el gasto se reparte por días entre esas fechas.',
                ]);
                $tocados++;
            }
        }

        return $tocados;
    }

    /** Pagos a docentes que todavía no tienen su gasto (por ejemplo, los registrados antes de este cambio). */
    public function completarPagosDocentes(): int
    {
        $conGasto = DB::table('gasto')->where('origen', self::HONORARIO)->whereNull('deleted_at')->pluck('origen_ref')->all();
        $faltan = DB::table('web_pago_sueldo')->where('estado', 1)->pluck('id')
            ->reject(fn ($id) => in_array((string) $id, $conGasto, true));

        foreach ($faltan as $id) {
            $this->alPagarDocente((int) $id);
        }

        return $faltan->count();
    }

    // ── Internos ────────────────────────────────────────────────────────────

    /**
     * Cortes de Facebook como puntos (fecha, gasto acumulado). Si la campaña empezó antes del primer corte,
     * se agrega (inicio, 0): lo que ya había gastado se reparte por días desde que empezó.
     *
     * @return array<int, array{0: Carbon, 1: float}>
     */
    private function puntos(object $campana, $cortes): array
    {
        $puntos = $cortes->map(fn ($m) => [Carbon::parse($m->fecha_corte)->startOfDay(), (float) $m->gasto_periodo])->values()->all();
        if ($puntos && $campana->fecha_inicio) {
            $inicio = Carbon::parse($campana->fecha_inicio)->startOfDay()->subDay();
            if ($inicio->lt($puntos[0][0])) {
                array_unshift($puntos, [$inicio, 0.0]);
            }
        }

        return $puntos;
    }

    /** Gasto acumulado al final de un día, interpolando por días entre dos cortes. */
    private function acumuladoAl(array $puntos, Carbon $dia): float
    {
        $dia = $dia->copy()->startOfDay();
        if (! $puntos || $dia->lt($puntos[0][0])) {
            return 0.0;
        }

        foreach ($puntos as $i => [$fecha, $valor]) {
            if ($dia->lte($fecha)) {
                [$fechaAntes, $valorAntes] = $puntos[$i - 1] ?? [$fecha, $valor];
                $dias = $fechaAntes->diffInDays($fecha);

                return $dias > 0
                    ? $valorAntes + ($valor - $valorAntes) * $fechaAntes->diffInDays($dia) / $dias
                    : $valor;
            }
        }

        return end($puntos)[1];
    }

    private function guardar(string $origen, string $ref, array $datos): void
    {
        $existente = DB::table('gasto')->where('origen', $origen)->where('origen_ref', $ref)->orderByDesc('id')->first();
        $datos += ['responsable' => 'Sistema'];

        if ($existente) {
            DB::table('gasto')->where('id', $existente->id)->update($datos + ['deleted_at' => null, 'updated_at' => now()]);

            return;
        }

        DB::table('gasto')->insert($datos + [
            'origen' => $origen, 'origen_ref' => $ref, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function categoria(string $nombre): int
    {
        return (int) (DB::table('categoria_gasto')->where('nombre', $nombre)->value('id')
            ?? DB::table('categoria_gasto')->insertGetId([
                'nombre' => $nombre, 'activo' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]));
    }
}
