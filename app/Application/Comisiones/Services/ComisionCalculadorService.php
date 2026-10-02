<?php

namespace App\Application\Comisiones\Services;

use App\Application\Comisiones\DTOs\ComisionLiquidacionDetalleDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ComisionCalculadorService
{
    private function primerPagoVerificadoSubquery()
    {
        return DB::table('t_pago as p')
            ->where('p.estado', 1)
            ->where('p.estado_verificacion', 'verificado')
            ->selectRaw(
                'p.id_pago, p.id_ins, p.id_us_cajero, p.fecha_deposito,
                ROW_NUMBER() OVER (PARTITION BY p.id_ins ORDER BY p.fecha_deposito ASC, p.id_pago ASC) as rn'
            );
    }

    public function inscritosElegibles(int $vendedorId, string $fechaDesde, string $fechaHasta): Collection
    {
        $usuarioId = DB::table('vendedores')->where('id', $vendedorId)->value('usuario_id');

        if ($usuarioId === null) {
            return collect();
        }

        return DB::query()
            ->fromSub($this->primerPagoVerificadoSubquery(), 'pp')
            ->join('t_inscripcion as ins', 'ins.id_ins', '=', 'pp.id_ins')
            ->join('t_programa as prog', 'prog.id_imp', '=', 'ins.id_imp')
            ->join('web_categoria_programa as cat', 'cat.id', '=', 'prog.categoria_web_id')
            ->where('prog.vendedor_id', $vendedorId)
            ->where('pp.rn', 1)
            ->where('pp.id_us_cajero', $usuarioId)
            ->whereBetween('pp.fecha_deposito', [$fechaDesde, $fechaHasta])
            ->whereNotIn('pp.id_ins', function ($sub) {
                $sub->select('d.id_ins')
                    ->from('comisiones_liquidacion_detalle as d')
                    ->join('comisiones_liquidacion as l', 'l.id', '=', 'd.comisiones_liquidacion_id')
                    ->where('l.estado', '!=', 'anulado');
            })
            ->select([
                'pp.id_ins',
                'cat.id as categoria_id',
                'cat.nombre as categoria_nombre',
                'cat.comision_monto',
                'pp.id_pago',
                'pp.fecha_deposito',
            ])
            ->orderBy('pp.fecha_deposito')
            ->get();
    }

    public function calcular(int $vendedorId, string $fechaDesde, string $fechaHasta): array
    {
        $inscritos = $this->inscritosElegibles($vendedorId, $fechaDesde, $fechaHasta);

        return [
            'inscritos'       => $inscritos->map(fn ($i) => ComisionLiquidacionDetalleDTO::fromModel($i))->all(),
            'total_inscritos' => $inscritos->count(),
            'monto_comision'  => round((float) $inscritos->sum('comision_monto'), 2),
        ];
    }
}
