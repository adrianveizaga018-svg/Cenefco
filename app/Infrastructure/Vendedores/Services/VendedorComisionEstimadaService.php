<?php

namespace App\Infrastructure\Vendedores\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VendedorComisionEstimadaService
{
    private function primerPagoVerificadoSubquery(): Builder
    {
        return DB::table('t_pago as p')
            ->where('p.estado', 1)
            ->where('p.estado_verificacion', 'verificado')
            ->selectRaw(
                'p.id_ins, p.id_us_cajero,
                ROW_NUMBER() OVER (PARTITION BY p.id_ins ORDER BY p.fecha_deposito ASC, p.id_pago ASC) as rn'
            );
    }

    private function inscritosElegiblesSubquery(): Builder
    {
        return DB::table('t_inscripcion as ins')
            ->join('t_programa as prog', 'prog.id_imp', '=', 'ins.id_imp')
            ->join('vendedores as v', 'v.id', '=', 'prog.vendedor_id')
            ->joinSub($this->primerPagoVerificadoSubquery(), 'pp', function ($join) {
                $join->on('pp.id_ins', '=', 'ins.id_ins')->where('pp.rn', 1);
            })
            ->where('ins.estado', 1)
            ->whereColumn('pp.id_us_cajero', 'v.usuario_id')
            ->selectRaw('ins.id_imp, COUNT(DISTINCT ins.id_ins) as total_inscritos')
            ->groupBy('ins.id_imp');
    }

    public function cursosPorVendedor(int $vendedorId): Collection
    {
        return DB::table('t_programa as p')
            ->leftJoinSub($this->inscritosElegiblesSubquery(), 'insc', 'insc.id_imp', '=', 'p.id_imp')
            ->leftJoin('web_categoria_programa as cat', 'cat.id', '=', 'p.categoria_web_id')
            ->where('p.vendedor_id', $vendedorId)
            ->select([
                'p.id_programa',
                'p.nombre_programa',
                DB::raw('COALESCE(insc.total_inscritos, 0) as total_inscritos'),
                DB::raw("cat.nombre as categoria_nombre"),
                DB::raw('COALESCE(cat.comision_monto, 0) as comision_monto'),
                DB::raw('COALESCE(insc.total_inscritos, 0) * COALESCE(cat.comision_monto, 0) as comision_estimada'),
            ])
            ->orderByDesc('total_inscritos')
            ->orderBy('p.nombre_programa')
            ->get();
    }

    public function comisionEstimadaPorVendedorQuery(): Builder
    {
        return DB::table('t_programa as p')
            ->leftJoinSub($this->inscritosElegiblesSubquery(), 'insc', 'insc.id_imp', '=', 'p.id_imp')
            ->leftJoin('web_categoria_programa as cat', 'cat.id', '=', 'p.categoria_web_id')
            ->whereNotNull('p.vendedor_id')
            ->groupBy('p.vendedor_id')
            ->selectRaw('p.vendedor_id, SUM(COALESCE(insc.total_inscritos, 0) * COALESCE(cat.comision_monto, 0)) as comision_estimada');
    }
}
