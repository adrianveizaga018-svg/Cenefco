<?php

namespace App\Application\Comisiones\Services;

use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * La tabla de comisiones la mantiene Administración/Contabilidad: tipos de producto, rangos de precio
 * con su monto, qué programa es de qué tipo, y los montos acordados en ventas negociadas.
 */
class TablaComisionesService
{
    public function tabla(): array
    {
        $rangos = DB::table('comision_rangos')->orderBy('desde')->get()->groupBy('producto_id');

        $productos = DB::table('comision_productos')->orderBy('orden')->orderBy('id')->get()
            ->map(fn ($p) => [
                'id'                     => (int) $p->id,
                'nombre'                 => $p->nombre,
                'modalidad'              => $p->modalidad,
                'porcentaje_minimo_pago' => (int) $p->porcentaje_minimo_pago,
                'activo'                 => (bool) $p->activo,
                'rangos'                 => ($rangos[$p->id] ?? collect())->map(fn ($r) => [
                    'desde' => (float) $r->desde,
                    'hasta' => $r->hasta !== null ? (float) $r->hasta : null,
                    'monto' => (float) $r->monto,
                ])->values()->all(),
            ])->all();

        $programas = DB::table('t_programa')->where('estado', 1)->orderBy('nombre_programa')
            ->get(['id_programa', 'nombre_programa', 'comision_producto_id', 'costo_monto'])
            ->map(fn ($p) => [
                'id_programa' => (int) $p->id_programa,
                'nombre'      => $p->nombre_programa,
                'producto_id' => $p->comision_producto_id ? (int) $p->comision_producto_id : null,
                'precio'      => $p->costo_monto !== null ? (float) $p->costo_monto : null,
            ])->all();

        return ['productos' => $productos, 'programas' => $programas];
    }

    /** Crea o actualiza un tipo de producto con sus rangos. */
    public function guardarProducto(?int $id, array $datos): int
    {
        $rangos = collect($datos['rangos'] ?? [])->map(fn ($r) => [
            'desde' => round((float) ($r['desde'] ?? 0), 2),
            'hasta' => isset($r['hasta']) && $r['hasta'] !== '' && $r['hasta'] !== null ? round((float) $r['hasta'], 2) : null,
            'monto' => round((float) $r['monto'], 2),
        ])->sortBy('desde')->values();

        if ($datos['modalidad'] === 'rangos' && $rangos->isEmpty()) {
            throw new DomainException('Agrega al menos un rango con su monto, o elige "Se negocia".');
        }
        foreach ($rangos as $i => $r) {
            if ($r['hasta'] !== null && $r['hasta'] < $r['desde']) {
                throw new DomainException("En el rango {$r['desde']}–{$r['hasta']} el \"hasta\" es menor que el \"desde\".");
            }
            $siguiente = $rangos[$i + 1] ?? null;
            if ($siguiente && ($r['hasta'] === null || $siguiente['desde'] <= $r['hasta'])) {
                throw new DomainException('Hay rangos que se cruzan: revisa los montos "desde" y "hasta".');
            }
        }

        return DB::transaction(function () use ($id, $datos, $rangos) {
            $fila = [
                'nombre'                 => trim($datos['nombre']),
                'modalidad'              => $datos['modalidad'],
                'porcentaje_minimo_pago' => (int) ($datos['porcentaje_minimo_pago'] ?? 50),
                'activo'                 => (bool) ($datos['activo'] ?? true),
                'updated_at'             => now(),
            ];
            if ($id) {
                if (! DB::table('comision_productos')->where('id', $id)->update($fila) && ! DB::table('comision_productos')->where('id', $id)->exists()) {
                    throw new DomainException('Tipo de producto no encontrado.');
                }
            } else {
                $id = DB::table('comision_productos')->insertGetId($fila + [
                    'orden' => (int) DB::table('comision_productos')->max('orden') + 1, 'created_at' => now(),
                ]);
            }

            DB::table('comision_rangos')->where('producto_id', $id)->delete();
            if ($datos['modalidad'] === 'rangos') {
                DB::table('comision_rangos')->insert($rangos->map(fn ($r) => $r + ['producto_id' => $id])->all());
            }

            return $id;
        });
    }

    /** @param array<int, array{id_programa: int, producto_id: ?int}> $asignaciones */
    public function asignarProgramas(array $asignaciones): int
    {
        $n = 0;
        foreach ($asignaciones as $a) {
            $n += DB::table('t_programa')->where('id_programa', (int) $a['id_programa'])
                ->update(['comision_producto_id' => $a['producto_id'] ? (int) $a['producto_id'] : null]);
        }

        return $n;
    }

    /** Monto acordado (negociación) y/o vendedor corregido para una venta. Queda quién y por qué. */
    public function acordar(int $idIns, ?float $monto, ?int $vendedorId, string $motivo, int $usuarioId): void
    {
        if (! DB::table('t_inscripcion')->where('id_ins', $idIns)->exists()) {
            throw new DomainException('Inscripción no encontrada.');
        }
        if ($monto === null && $vendedorId === null) {
            throw new DomainException('Indica el monto acordado o el vendedor.');
        }
        if ($this->yaLiquidada($idIns)) {
            throw new DomainException('Esta venta ya está en una liquidación: anúlala primero para cambiar su comisión.');
        }

        DB::table('comision_acuerdos')->updateOrInsert(['id_ins' => $idIns], [
            'monto' => $monto !== null ? round($monto, 2) : null, 'vendedor_id' => $vendedorId,
            'motivo' => mb_substr(trim($motivo), 0, 500), 'usuario_id' => $usuarioId,
            'updated_at' => now(), 'created_at' => now(),
        ]);
    }

    public function quitarAcuerdo(int $idIns): void
    {
        if ($this->yaLiquidada($idIns)) {
            throw new DomainException('Esta venta ya está en una liquidación: anúlala primero para cambiar su comisión.');
        }
        DB::table('comision_acuerdos')->where('id_ins', $idIns)->delete();
    }

    private function yaLiquidada(int $idIns): bool
    {
        return DB::table('comisiones_liquidacion_detalle as d')
            ->join('comisiones_liquidacion as l', 'l.id', '=', 'd.comisiones_liquidacion_id')
            ->where('d.id_ins', $idIns)->where('l.estado', '!=', 'anulado')->exists();
    }
}
