<?php

namespace App\Application\CompromisosCobro\Services;

use Illuminate\Support\Facades\DB;

class VendedorDeInscripcionResolver
{
    public function resolverUsuarioId(int $idIns): ?int
    {
        return DB::table('t_inscripcion as ins')
            ->join('t_programa as p', 'p.id_imp', '=', 'ins.id_imp')
            ->join('vendedores as v', 'v.id', '=', 'p.vendedor_id')
            ->where('ins.id_ins', $idIns)
            ->whereNotNull('v.usuario_id')
            ->where('v.activo', true)
            ->orderBy('p.id_us_reg')
            ->value('v.usuario_id');
    }
}
