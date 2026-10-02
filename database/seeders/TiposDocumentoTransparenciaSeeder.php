<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposDocumentoTransparenciaSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            'Presupuesto Institucional',
            'Plan Operativo Anual (POA)',
            'Memoria Institucional',
            'Informe de Gestión',
            'Contrato / Licitación',
            'Estados Financieros',
            'Planilla Salarial',
            'Reglamento Interno',
            'Organigramas',
            'Indicadores de Gestión',
        ];

        foreach ($tipos as $nombre) {
            DB::table('tipos_documento_transparencia')->updateOrInsert(
                ['nombre' => $nombre],
                ['nombre' => $nombre, 'activo' => true]
            );
        }
    }
}
