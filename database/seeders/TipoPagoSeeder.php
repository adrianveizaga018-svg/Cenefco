<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoPagoSeeder extends Seeder
{
    public function run(): void
    {
        
        $tipos = [
            [1, 'Cuota 1',         'Cuotas',    1],
            [2, 'Cuota 2',         'Cuotas',    1],
            [3, 'Cuota 3',         'Cuotas',    1],
            [4, 'Pago Único',      'Contado',   2],
            [5, 'Pago al Contado', 'Contado',   2],
            [6, 'Combo',           'Combo',      3],
            [7, 'Descuento',       'Descuento',  4],
            [8, 'Matrícula',       'Matrícula',  5],
            [9, 'Beca Parcial',    'Beca',       6],
        ];

        $now = now();

        foreach ($tipos as [$id, $titulo, $grupo, $catId]) {
            DB::table('t_tipopago')->insertOrIgnore([
                'id_tipopago'          => $id,
                'id_us_reg'            => 1,
                'num_tipopago'         => $id,
                'titulo'               => $titulo,
                'id_categoriatipopago' => $catId,
                'grupo_pago'           => $grupo,
                'fecha_reg'            => $now,
                'estado'               => 1,
                'per_modificar'        => 0,
            ]);
        }

        $total = DB::table('t_tipopago')->count();
        $this->command->info("✓ {$total} tipos de pago en t_tipopago.");
    }
}
