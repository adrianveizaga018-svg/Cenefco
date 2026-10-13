<?php

namespace App\Application\Comisiones\Services;

use App\Application\Comisiones\DTOs\ComisionLiquidacionDetalleDTO;
use App\Application\Inscripciones\Services\InscripcionCuotasService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Comisión por venta, según la tabla que configura Administración/Contabilidad:
 *  - monto fijo según el tipo de producto del programa y el rango de su precio;
 *  - la venta comisiona cuando el estudiante pagó al menos el % mínimo del producto (por defecto 50 %):
 *    las reservas no comisionan;
 *  - los productos "a negociar" (diplomados, congresos) y los precios fuera de la tabla esperan a que
 *    alguien acuerde el monto; ese acuerdo también puede corregir el vendedor o el monto de cualquier venta.
 *
 * Vendedor de una venta: el acordado, el registrado en la inscripción, el del embudo de ventas
 * o, si nada de eso, el vendedor que cobró el primer pago en caja.
 */
class ComisionCalculadorService
{
    public const TABLA = 'tabla';
    public const ACORDADA = 'acordada';
    public const POR_NEGOCIAR = 'por_negociar';
    public const SIN_TIPO = 'sin_tipo';
    public const FUERA_DE_TABLA = 'fuera_de_tabla';

    public function __construct(private readonly InscripcionCuotasService $cuotas) {}

    /** Lo que se puede liquidar a un vendedor en el periodo (solo ventas con monto definido). */
    public function calcular(int $vendedorId, string $fechaDesde, string $fechaHasta): array
    {
        $ventas = $this->ventas($fechaDesde, $fechaHasta, $vendedorId);
        $conMonto = $ventas->filter(fn ($v) => $v->comision_monto !== null)->values();

        return [
            'inscritos'       => $conMonto->map(fn ($v) => ComisionLiquidacionDetalleDTO::fromModel($v))->all(),
            'pendientes'      => $ventas->filter(fn ($v) => $v->comision_monto === null)->map(fn ($v) => ComisionLiquidacionDetalleDTO::fromModel($v))->values()->all(),
            'total_inscritos' => $conMonto->count(),
            'monto_comision'  => round((float) $conMonto->sum('comision_monto'), 2),
        ];
    }

    /**
     * Ventas que llegaron al pago mínimo dentro del periodo y aún no están en una liquidación.
     * Con $vendedorId, solo las suyas; sin él, todas (incluidas las que no tienen vendedor).
     */
    public function ventas(string $fechaDesde, string $fechaHasta, ?int $vendedorId = null, bool $incluirLiquidadas = false): Collection
    {
        $productos = DB::table('comision_productos')->where('activo', true)->get()->keyBy('id');
        $rangos = DB::table('comision_rangos')->orderBy('desde')->get()->groupBy('producto_id');
        $liquidadas = $incluirLiquidadas ? [] : DB::table('comisiones_liquidacion_detalle as d')
            ->join('comisiones_liquidacion as l', 'l.id', '=', 'd.comisiones_liquidacion_id')
            ->where('l.estado', '!=', 'anulado')->pluck('d.id_ins')->map(fn ($id) => (int) $id)->flip()->all();

        // Candidatas: inscripciones con algún pago válido hasta el fin del periodo
        $candidatas = DB::table('t_inscripcion as i')
            ->join('t_pago as p', 'p.id_ins', '=', 'i.id_ins')
            ->where('i.estado', 1)
            ->where('p.estado', 1)
            ->where(fn ($q) => $q->whereNull('p.estado_verificacion')->orWhere('p.estado_verificacion', '!=', InscripcionCuotasService::COMPROBANTE_ESTUDIANTE))
            ->where('p.fecha_deposito', '<=', $fechaHasta)
            ->groupBy('i.id_ins')
            ->havingRaw('MAX(p.fecha_deposito) >= ?', [$fechaDesde])
            ->pluck('i.id_ins');

        $resultado = collect();
        foreach ($candidatas as $idIns) {
            if (isset($liquidadas[(int) $idIns])) {
                continue;
            }
            $venta = $this->venta((int) $idIns, $productos, $rangos);
            if (! $venta || $venta->fecha_deposito < $fechaDesde || $venta->fecha_deposito > $fechaHasta) {
                continue;
            }
            if ($vendedorId !== null && $venta->vendedor_id !== $vendedorId) {
                continue;
            }
            $resultado->push($venta);
        }

        return $resultado->sortBy('fecha_deposito')->values();
    }

    /** Una venta con su comisión, o null si todavía no llegó al pago mínimo (reserva) o es gratuita. */
    private function venta(int $idIns, Collection $productos, Collection $rangos): ?object
    {
        $ins = DB::table('t_inscripcion as i')
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('u.id_us', '=', 'i.id_us')->whereRaw('u.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us)');
            })
            ->leftJoin('t_imparte as imp', function ($j) {
                $j->on('imp.id_imp', '=', 'i.id_imp')->whereRaw('imp.id_us_reg = (SELECT MIN(i2.id_us_reg) FROM t_imparte i2 WHERE i2.id_imp = imp.id_imp)');
            })
            ->leftJoin('t_programa as prog', 'prog.id_programa', '=', 'imp.id_mat')
            ->where('i.id_ins', $idIns)
            ->first(['i.id_ins', 'i.id_us', 'i.id_vendedor', 'prog.id_programa', 'prog.nombre_programa', 'prog.comision_producto_id',
                'u.nombre', 'u.appaterno', 'u.apmaterno']);
        if (! $ins) {
            return null;
        }

        $resumen = $this->cuotas->sincronizar($idIns);
        $precio = round((float) ($resumen->total_plan ?? 0), 2);
        if ($precio <= 0) {
            return null; // gratuita o sin plan: no hay venta que comisionar
        }

        $producto = $ins->comision_producto_id ? ($productos[$ins->comision_producto_id] ?? null) : null;
        $minimo = $precio * (($producto->porcentaje_minimo_pago ?? 50) / 100);

        // Fecha en que lo pagado alcanzó el mínimo: ahí se gana la comisión
        $pagos = DB::table('t_pago')->where('id_ins', $idIns)->where('estado', 1)
            ->where(fn ($q) => $q->whereNull('estado_verificacion')->orWhere('estado_verificacion', '!=', InscripcionCuotasService::COMPROBANTE_ESTUDIANTE))
            ->orderBy('fecha_deposito')->orderBy('id_pago')->get(['id_pago', 'monto_pagado', 'fecha_deposito', 'id_us_cajero']);
        $acumulado = 0.0;
        $alcanzado = null;
        foreach ($pagos as $p) {
            $acumulado += (float) $p->monto_pagado;
            if ($acumulado + 0.009 >= $minimo) {
                $alcanzado = $p;
                break;
            }
        }
        if (! $alcanzado) {
            return null;
        }

        $acuerdo = DB::table('comision_acuerdos')->where('id_ins', $idIns)->first();
        [$monto, $estado, $regla] = $this->monto($producto, $rangos[$producto->id ?? 0] ?? collect(), $precio, $acuerdo);
        $vendedorId = $this->vendedor($ins, $pagos->first(), $acuerdo);

        return (object) [
            'id_ins'           => $idIns,
            'id_pago'          => (int) $alcanzado->id_pago,
            'fecha_deposito'   => substr((string) $alcanzado->fecha_deposito, 0, 10),
            'categoria_id'     => $producto->id ?? null,
            'categoria_nombre' => $producto->nombre ?? null,
            'comision_monto'   => $monto,
            'estado_comision'  => $estado,
            'regla'            => $regla,
            'precio'           => $precio,
            'pagado'           => round((float) ($resumen->total_pagado ?? $acumulado), 2),
            'estudiante'       => trim(implode(' ', array_filter([$ins->nombre, $ins->appaterno, $ins->apmaterno]))) ?: null,
            'id_programa'      => $ins->id_programa ? (int) $ins->id_programa : null,
            'programa'         => $ins->nombre_programa,
            'vendedor_id'      => $vendedorId,
            'vendedor_nombre'  => $vendedorId ? $this->nombreVendedor($vendedorId) : null,
            'motivo_acuerdo'   => $acuerdo->motivo ?? null,
        ];
    }

    /** @return array{0: ?float, 1: string, 2: string} [monto, estado, regla en palabras] */
    private function monto(?object $producto, Collection $rangos, float $precio, ?object $acuerdo): array
    {
        if ($acuerdo && $acuerdo->monto !== null) {
            return [round((float) $acuerdo->monto, 2), self::ACORDADA, 'Monto acordado'];
        }
        if (! $producto) {
            return [null, self::SIN_TIPO, 'El programa no tiene tipo de producto para comisiones'];
        }
        if ($producto->modalidad === 'negociar') {
            return [null, self::POR_NEGOCIAR, "{$producto->nombre}: se negocia"];
        }
        foreach ($rangos as $r) {
            if ($precio >= (float) $r->desde && ($r->hasta === null || $precio <= (float) $r->hasta)) {
                $tramo = match (true) {
                    $r->hasta === null && (float) $r->desde <= 0 => 'cualquier precio',
                    $r->hasta === null => 'desde Bs '.(float) $r->desde,
                    default => 'Bs '.(float) $r->desde.' a '.(float) $r->hasta,
                };

                return [round((float) $r->monto, 2), self::TABLA, "{$producto->nombre}, {$tramo}"];
            }
        }

        return [null, self::FUERA_DE_TABLA, "{$producto->nombre}: precio Bs {$precio} fuera de la tabla"];
    }

    /** Vendedor de una inscripción, con la misma regla que las comisiones (para el ranking). */
    public function vendedorDe(int $idIns): ?int
    {
        $ins = DB::table('t_inscripcion')->where('id_ins', $idIns)->first(['id_ins', 'id_vendedor']);
        if (! $ins) {
            return null;
        }
        $primerPago = DB::table('t_pago')->where('id_ins', $idIns)->where('estado', 1)
            ->orderBy('fecha_deposito')->orderBy('id_pago')->first(['id_us_cajero']);

        return $this->vendedor($ins, $primerPago, DB::table('comision_acuerdos')->where('id_ins', $idIns)->first());
    }

    private function vendedor(object $ins, ?object $primerPago, ?object $acuerdo): ?int
    {
        if ($acuerdo && $acuerdo->vendedor_id) {
            return (int) $acuerdo->vendedor_id;
        }
        if ($ins->id_vendedor) {
            return (int) $ins->id_vendedor;
        }
        $delEmbudo = DB::table('leads')->where('id_ins', $ins->id_ins)->whereNull('deleted_at')->whereNotNull('vendedor_asignado_id')->value('vendedor_asignado_id');
        if ($delEmbudo) {
            return (int) $delEmbudo;
        }
        if ($primerPago?->id_us_cajero) {
            $cobro = DB::table('vendedores')->where('usuario_id', $primerPago->id_us_cajero)->whereNull('deleted_at')->value('id');

            return $cobro ? (int) $cobro : null;
        }

        return null;
    }

    private function nombreVendedor(int $id): ?string
    {
        static $cache = [];

        return $cache[$id] ??= (fn ($v) => $v ? trim($v->nombre.' '.$v->apellido) : null)(
            DB::table('vendedores')->where('id', $id)->first(['nombre', 'apellido'])
        );
    }
}
