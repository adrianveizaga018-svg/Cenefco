<?php

namespace App\Application\Inscripciones\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Cronograma de cuotas de cada inscripción (t_inscripcion_cuotas): única fuente de la deuda.
 *
 * El monto de cada cuota sale del plan. Lo pagado NO se marca a mano: `sincronizar()`
 * reparte en orden todos los pagos válidos de la inscripción (pagos + descuentos,
 * vengan de Caja, del detalle, del portal o de una transferencia). Así una cuota
 * puede quedar parcial y Caja, la ficha y el estudiante ven el mismo saldo.
 */
class InscripcionCuotasService
{
    private const TOLERANCIA = 0.005;

    public function generarCuotasParaInscripcion(int $idIns, int $idPlan, ?int $idPagoInicial = null): void
    {
        $this->crearCronograma($idIns, $idPlan);

        if ($idPagoInicial) {
            DB::table('t_inscripcion_cuotas')
                ->where('id_ins', $idIns)->orderBy('nro_cuota')->limit(1)
                ->update(['id_pago' => $idPagoInicial]);
        }

        $this->sincronizar($idIns);
    }

    /**
     * Recalcula lo pagado de cada cuota. Si la inscripción tiene plan pero aún no tiene
     * cronograma (inscripciones del portal, bot, diplomados o antiguas), lo crea.
     *
     * @return object|null resumen {total_plan, total_pagado, pendiente, cuotas_totales, cuotas_pagadas}; null si no hay cronograma
     */
    public function sincronizar(int $idIns): ?object
    {
        $cuotas = $this->cuotas($idIns);

        if ($cuotas->isEmpty()) {
            $idPlan = DB::table('t_inscripcion')->where('id_ins', $idIns)->value('id_plan');
            if (! $idPlan || ! $this->crearCronograma($idIns, (int) $idPlan)) {
                return null;
            }
            $cuotas = $this->cuotas($idIns);
        }

        $pagado     = $this->totalAplicable($idIns);
        $disponible = $pagado;

        foreach ($cuotas as $c) {
            $monto    = round((float) $c->monto_a_pagar, 2);
            $aplicado = round(min($monto, max(0.0, $disponible)), 2);
            $disponible = round($disponible - $aplicado, 2);

            $estado = match (true) {
                $monto - $aplicado <= self::TOLERANCIA => 'pagado',
                $aplicado > 0                          => 'parcial',
                default                                => 'pendiente',
            };

            if ($c->estado !== $estado || abs((float) $c->monto_pagado - $aplicado) > self::TOLERANCIA) {
                DB::table('t_inscripcion_cuotas')->where('id', $c->id)->update([
                    'estado'       => $estado,
                    'monto_pagado' => $aplicado,
                    'updated_at'   => now(),
                ]);
            }
            $c->estado       = $estado;
            $c->monto_pagado = $aplicado;
        }

        $totalPlan = (float) $cuotas->sum(fn ($c) => (float) $c->monto_a_pagar);

        return (object) [
            'total_plan'     => $totalPlan,
            'total_pagado'   => $pagado,
            'pendiente'      => round(max(0.0, $totalPlan - (float) $cuotas->sum('monto_pagado')), 2),
            'cuotas_totales' => $cuotas->count(),
            'cuotas_pagadas' => $cuotas->where('estado', 'pagado')->count(),
            'cuotas'         => $cuotas->values()->all(),
        ];
    }

    /** Sincroniza todas las inscripciones activas de un estudiante (t_usuario.id_us). */
    public function sincronizarEstudiante(int $idUs): void
    {
        DB::table('t_inscripcion')->where('id_us', $idUs)->where('estado', 1)->pluck('id_ins')
            ->each(fn ($idIns) => $this->sincronizar((int) $idIns));
    }

    /** Comprobante subido por el estudiante que Caja aún no aprobó: todavía no descuenta deuda. */
    public const COMPROBANTE_ESTUDIANTE = 'enviado_estudiante';

    /**
     * Pagos que cuentan para la deuda: todos los activos (los registra el personal),
     * menos los comprobantes del estudiante que Caja todavía no aprobó.
     */
    public function totalAplicable(int $idIns): float
    {
        $ins = DB::table('t_inscripcion')->where('id_ins', $idIns)->first(['id_us', 'id_plan']);

        return round((float) DB::table('t_pago')
            // Pagos antiguos sin id_ins: se reconocen por la cuota del plan, igual que en el detalle
            ->where(fn ($q) => $q->where('id_ins', $idIns)->when($ins?->id_plan, fn ($q) => $q->orWhere(fn ($l) => $l
                ->whereNull('id_ins')
                ->where('id_us', $ins->id_us)
                ->whereIn('id_fechapago', DB::table('t_fechapago')->where('id_plan', $ins->id_plan)->select('id_fechapago')))))
            ->where('estado', 1)
            ->where(fn ($q) => $q->whereNull('estado_verificacion')->orWhere('estado_verificacion', '!=', self::COMPROBANTE_ESTUDIANTE))
            ->sum(DB::raw('COALESCE('.\App\Shared\Kernel\Support\SqlCompat::castDecimal('monto_pagado').', 0) + COALESCE('.\App\Shared\Kernel\Support\SqlCompat::castDecimal('monto_descuento_extra').', 0)')), 2);
    }

    private function cuotas(int $idIns): Collection
    {
        return DB::table('t_inscripcion_cuotas')
            ->where('id_ins', $idIns)
            ->orderBy('nro_cuota')->orderBy('id')
            ->get();
    }

    /** Crea el cronograma desde el plan. Un plan sin cuotas cargadas con costo > 0 es un pago único. */
    private function crearCronograma(int $idIns, int $idPlan): bool
    {
        $plan = DB::table('t_plan')->where('id_plan', $idPlan)->first();
        if (! $plan) {
            return false;
        }

        $templates = DB::table('t_fechapago')
            ->where('id_plan', $idPlan)
            ->where('estado', 1)
            ->orderBy('nro_pago')
            ->get();

        $fechaIns   = DB::table('t_inscripcion')->where('id_ins', $idIns)->value('fecha_ins');
        $base       = $fechaIns ? \Carbon\Carbon::parse($fechaIns) : now();
        $modoFechas = $plan->modo_fechas ?? 'fijo';
        $filas      = [];

        foreach ($templates as $i => $t) {
            // Hay planes marcados "fijo" con cuotas en días: si falta la fecha fija se usan los días,
            // y si no hay ninguno vence el día de la inscripción (la columna no admite vacío)
            $relativa = $t->dias_desde_inscripcion !== null && ($modoFechas === 'relativo' || ! $t->fecha_fin);
            $vencimiento = $relativa
                ? $base->copy()->addDays((int) $t->dias_desde_inscripcion)->format('Y-m-d')
                : ($t->fecha_fin ?: $base->format('Y-m-d'));

            $filas[] = [
                'id_fechapago_template' => $t->id_fechapago,
                'nro_cuota'             => $t->nro_pago ?: ($i + 1),
                'descripcion'           => $t->tipo_tramite ?: ('Cuota ' . ($t->nro_pago ?: ($i + 1))),
                'monto_a_pagar'         => $t->monto_a_pagar,
                'fecha_vencimiento'     => $vencimiento,
            ];
        }

        if (! $filas && is_numeric($plan->costo) && (float) $plan->costo > 0) {
            $filas[] = [
                'id_fechapago_template' => null,
                'nro_cuota'             => 1,
                'descripcion'           => 'Pago único',
                'monto_a_pagar'         => (float) $plan->costo,
                'fecha_vencimiento'     => $base->format('Y-m-d'),
            ];
        }

        if (! $filas) {
            return false;
        }

        DB::table('t_inscripcion_cuotas')->insert(array_map(fn ($f) => $f + [
            'id_ins'       => $idIns,
            'estado'       => 'pendiente',
            'monto_pagado' => 0,
            'id_pago'      => null,
            'created_at'   => now(),
            'updated_at'   => now(),
        ], $filas));

        return true;
    }
}
