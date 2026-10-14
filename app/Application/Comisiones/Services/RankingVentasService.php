<?php

namespace App\Application\Comisiones\Services;

use App\Application\Inscripciones\Services\InscripcionCuotasService;
use App\Enums\DestinatarioEnum;
use App\Enums\PrioridadEnum;
use App\Services\NotificacionService;
use App\Shared\Kernel\Support\SqlCompat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Ranking de vendedores del mes y podio (1°, 2°, 3°) para generar competencia.
 * Se ordena por lo recaudado en el mes de sus ventas; a igualdad, por cantidad de ventas cerradas
 * (las que llegaron al pago mínimo ese mes, igual que para la comisión).
 */
class RankingVentasService
{
    private const MEDALLAS = [1 => '🥇', 2 => '🥈', 3 => '🥉'];

    public function __construct(
        private readonly ComisionCalculadorService $comisiones,
        private readonly NotificacionService $notificaciones,
    ) {}

    public function ranking(string $periodo, ?int $usuarioId = null): array
    {
        [$desde, $hasta] = $this->rango($periodo);

        $ventas = $this->comisiones->ventas($desde, $hasta, null, true)
            ->whereNotNull('vendedor_id')->countBy('vendedor_id');

        $recaudado = [];
        $pagos = DB::table('t_pago')->where('estado', 1)->whereNotNull('id_ins')
            ->where(fn ($q) => $q->whereNull('estado_verificacion')->orWhere('estado_verificacion', '!=', InscripcionCuotasService::COMPROBANTE_ESTUDIANTE))
            ->whereBetween('fecha_deposito', [$desde, $hasta])
            ->groupBy('id_ins')->selectRaw('id_ins, '.SqlCompat::sumaMonto('monto_pagado').' as total')->pluck('total', 'id_ins');
        foreach ($pagos as $idIns => $total) {
            if ($vendedor = $this->comisiones->vendedorDe((int) $idIns)) {
                $recaudado[$vendedor] = ($recaudado[$vendedor] ?? 0) + (float) $total;
            }
        }

        $filas = DB::table('vendedores')->whereNull('deleted_at')
            ->where(fn ($q) => $q->where('activo', true)->orWhereIn('id', array_keys($recaudado)))
            ->get(['id', 'nombre', 'apellido', 'foto', 'meta_ventas', 'usuario_id'])
            ->map(fn ($v) => [
                'vendedor_id' => (int) $v->id,
                'nombre'      => trim($v->nombre.' '.$v->apellido),
                'foto'        => $v->foto,
                'ventas'      => (int) ($ventas[$v->id] ?? 0),
                'recaudado'   => round($recaudado[$v->id] ?? 0, 2),
                'meta'        => $v->meta_ventas !== null ? (float) $v->meta_ventas : null,
                'es_yo'       => $usuarioId !== null && (int) $v->usuario_id === $usuarioId,
            ])
            ->sort(fn ($a, $b) => [$b['recaudado'], $b['ventas']] <=> [$a['recaudado'], $a['ventas']])
            ->values();

        $puesto = 0;
        $lista = $filas->map(function ($f) use (&$puesto) {
            $f['puesto'] = $f['recaudado'] > 0 || $f['ventas'] > 0 ? ++$puesto : null;
            $f['progreso_meta'] = $f['meta'] ? min(100, round($f['recaudado'] / $f['meta'] * 100)) : null;

            return $f;
        })->all();

        return [
            'periodo'   => $periodo,
            'cerrado'   => Carbon::parse($hasta)->lt(now()->startOfDay()),
            'ranking'   => $lista,
            'total'     => round(array_sum($recaudado), 2),
        ];
    }

    /** Podios guardados de los meses anteriores. */
    public function historial(int $meses = 12): array
    {
        return DB::table('ventas_podio as p')->join('vendedores as v', 'v.id', '=', 'p.vendedor_id')
            ->orderByDesc('p.periodo')->orderBy('p.puesto')
            ->limit($meses * 3)
            ->get(['p.periodo', 'p.puesto', 'p.vendedor_id', 'p.recaudado', 'p.ventas', DB::raw(SqlCompat::trimConcat('v.nombre', "' '", "COALESCE(v.apellido, '')").' as nombre')])
            ->groupBy('periodo')
            ->map(fn ($podio, $periodo) => ['periodo' => $periodo, 'podio' => $podio->map(fn ($p) => [
                'puesto' => (int) $p->puesto, 'vendedor_id' => (int) $p->vendedor_id, 'nombre' => $p->nombre,
                'recaudado' => (float) $p->recaudado, 'ventas' => (int) $p->ventas,
            ])->values()->all()])
            ->values()->all();
    }

    /**
     * Cierra el mes: guarda el podio y felicita. Cada ganador recibe su felicitación y todo el equipo
     * de ventas ve el podio en la campanita. Si el mes ya se anunció, no se repite.
     */
    public function cerrarMes(string $periodo): array
    {
        $podio = collect($this->ranking($periodo)['ranking'])->whereNotNull('puesto')->take(3)->values();
        if (DB::table('ventas_podio')->where('periodo', $periodo)->whereNotNull('notificado_at')->exists()) {
            return ['podio' => $podio->all(), 'anunciado' => false];
        }

        DB::table('ventas_podio')->where('periodo', $periodo)->delete();
        foreach ($podio as $p) {
            DB::table('ventas_podio')->insert([
                'periodo' => $periodo, 'puesto' => $p['puesto'], 'vendedor_id' => $p['vendedor_id'],
                'recaudado' => $p['recaudado'], 'ventas' => $p['ventas'], 'created_at' => now(),
            ]);
        }
        if ($podio->isEmpty()) {
            return ['podio' => [], 'anunciado' => false];
        }

        $mes = $this->nombreMes($periodo);
        foreach ($podio as $p) {
            $usuario = DB::table('vendedores')->where('id', $p['vendedor_id'])->value('usuario_id');
            if (! $usuario) {
                continue;
            }
            $this->notificaciones->enviar(
                destinatario: DestinatarioEnum::USUARIO,
                tipo:         'podio_ventas',
                titulo:       self::MEDALLAS[$p['puesto']]." ¡Felicidades! Puesto {$p['puesto']} en ventas de {$mes}",
                mensaje:      $this->felicitacion($p, $mes),
                prioridad:    PrioridadEnum::ALTA,
                usuarioId:    (int) $usuario,
                urlAccion:    '/crm/ranking',
                icono:        'lucideTrophy',
                color:        '#d97706',
            );
        }

        $resumen = $podio->map(fn ($p) => self::MEDALLAS[$p['puesto']].' '.$p['nombre'].' (Bs '.number_format($p['recaudado'], 2, ',', '.').')')->join(' · ');
        $this->notificaciones->enviarAPermiso(
            permiso:   'crm.ver',
            tipo:      'podio_ventas',
            titulo:    "🏆 Podio de ventas de {$mes}",
            mensaje:   $resumen.'. ¡Felicitaciones! El ranking de este mes ya empezó.',
            prioridad: PrioridadEnum::MEDIA,
            urlAccion: '/crm/ranking',
            icono:     'lucideTrophy',
            color:     '#d97706',
        );

        DB::table('ventas_podio')->where('periodo', $periodo)->update(['notificado_at' => now()]);

        return ['podio' => $podio->all(), 'anunciado' => true];
    }

    private function felicitacion(array $p, string $mes): string
    {
        $monto = 'Bs '.number_format($p['recaudado'], 2, ',', '.');

        return match ($p['puesto']) {
            1 => "Fuiste el mejor vendedor de {$mes}: {$monto} recaudados y {$p['ventas']} venta(s) cerradas. ¡Gran trabajo, a defender el primer puesto!",
            2 => "Quedaste segundo en {$mes} con {$monto} y {$p['ventas']} venta(s). ¡Muy cerca de la cima, vamos por el primer puesto!",
            default => "Quedaste tercero en {$mes} con {$monto} y {$p['ventas']} venta(s). ¡Estás en el podio, sigue así!",
        };
    }

    /** @return array{0: string, 1: string} */
    private function rango(string $periodo): array
    {
        $inicio = Carbon::createFromFormat('Y-m-d', $periodo.'-01')->startOfDay();

        return [$inicio->toDateString(), $inicio->copy()->endOfMonth()->toDateString()];
    }

    private function nombreMes(string $periodo): string
    {
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        [$anio, $mes] = explode('-', $periodo);

        return $meses[(int) $mes - 1].' '.$anio;
    }
}
