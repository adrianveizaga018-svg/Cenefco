<?php

namespace Database\Seeders;

use App\Infrastructure\Gastos\Models\CategoriaGasto;
use App\Infrastructure\Gastos\Models\GastoRecurrente;
use Illuminate\Database\Seeder;

class GastoRecurrenteSeeder extends Seeder
{
    public function run(): void
    {
        $categoriaId = fn (string $nombre) => CategoriaGasto::where('nombre', $nombre)->value('id');

        $recurrentes = [
            ['categoria' => 'Alquiler de oficina',          'concepto' => 'Alquiler mensual oficina central', 'monto' => 3500.00, 'dia_del_mes' => 5],
            ['categoria' => 'Internet y telefonía',         'concepto' => 'Plan de internet corporativo',     'monto' => 350.00,  'dia_del_mes' => 15],
            ['categoria' => 'Servicios básicos (luz/agua)', 'concepto' => 'Factura de luz',                   'monto' => 420.00,  'dia_del_mes' => 10],
            ['categoria' => 'Servicios básicos (luz/agua)', 'concepto' => 'Factura de agua',                  'monto' => 180.00,  'dia_del_mes' => 10],
            ['categoria' => 'Software y licencias',         'concepto' => 'Hosting y dominio web',            'monto' => 40.00,   'dia_del_mes' => 1],
            ['categoria' => 'Software y licencias',         'concepto' => 'Licencia Zoom Pro (mensualizada)', 'monto' => 100.00,  'dia_del_mes' => 3],
        ];

        foreach ($recurrentes as $r) {
            $catId = $categoriaId($r['categoria']);
            if (! $catId) continue;

            GastoRecurrente::firstOrCreate(
                ['concepto' => $r['concepto']],
                [
                    'categoria_gasto_id' => $catId,
                    'monto'              => $r['monto'],
                    'dia_del_mes'        => $r['dia_del_mes'],
                    'activo'             => true,
                ]
            );
        }
    }
}
