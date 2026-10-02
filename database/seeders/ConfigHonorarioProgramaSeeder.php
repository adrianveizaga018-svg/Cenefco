<?php

namespace Database\Seeders;

use App\Infrastructure\Honorarios\Models\ConfigHonorarioPrograma;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigHonorarioProgramaSeeder extends Seeder
{
    public function run(): void
    {
        $programas = DB::table('t_programa')->select('id_programa', 'nombre_programa')->get();
        if ($programas->isEmpty()) {
            return;
        }

        $tipos = ['diplomado_fijo', 'rm_por_dia', 'aval_por_dia'];

        foreach ($programas as $i => $prog) {
            $tipo = $tipos[$i % count($tipos)];

            ConfigHonorarioPrograma::firstOrCreate(
                ['id_programa' => $prog->id_programa],
                [
                    'tipo_honorario' => $tipo,
                    'monto_fijo'     => $tipo === 'diplomado_fijo' ? 1500.00 : null,
                    'monto_por_dia'  => $tipo !== 'diplomado_fijo' ? 120.00 : null,
                ]
            );
        }
    }
}
