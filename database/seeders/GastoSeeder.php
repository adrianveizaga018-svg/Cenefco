<?php

namespace Database\Seeders;

use App\Infrastructure\Gastos\Models\CategoriaGasto;
use App\Infrastructure\Gastos\Models\Gasto;
use Illuminate\Database\Seeder;

class GastoSeeder extends Seeder
{
    public function run(): void
    {
        $categoriaId = fn (string $nombre) => CategoriaGasto::where('nombre', $nombre)->value('id');

        $gastos = [
            ['categoria' => 'Alquiler de oficina',          'concepto' => 'Alquiler oficina central — mayo',    'monto' => 3500.00, 'fecha' => '2026-05-05', 'responsable' => 'Administración'],
            ['categoria' => 'Alquiler de oficina',          'concepto' => 'Alquiler oficina central — junio',   'monto' => 3500.00, 'fecha' => '2026-06-05', 'responsable' => 'Administración'],
            ['categoria' => 'Servicios básicos (luz/agua)', 'concepto' => 'Factura de luz — mayo',              'monto' => 420.50,  'fecha' => '2026-05-10', 'responsable' => 'Administración'],
            ['categoria' => 'Servicios básicos (luz/agua)', 'concepto' => 'Factura de agua — mayo',             'monto' => 180.00,  'fecha' => '2026-05-10', 'responsable' => 'Administración'],
            ['categoria' => 'Internet y telefonía',         'concepto' => 'Plan de internet corporativo',       'monto' => 350.00,  'fecha' => '2026-05-15', 'responsable' => 'Administración'],
            ['categoria' => 'Material de escritorio',       'concepto' => 'Resmas de papel y útiles varios',    'monto' => 210.00,  'fecha' => '2026-05-18', 'responsable' => 'Secretaría'],
            ['categoria' => 'Mantenimiento de equipos',     'concepto' => 'Mantenimiento de impresoras',        'monto' => 150.00,  'fecha' => '2026-05-20', 'responsable' => 'Soporte Técnico'],
            ['categoria' => 'Publicidad y marketing',       'concepto' => 'Campaña Facebook Ads — mayo',        'monto' => 900.00,  'fecha' => '2026-05-01', 'responsable' => 'Marketing'],
            ['categoria' => 'Publicidad y marketing',       'concepto' => 'Diseño de banners promocionales',    'monto' => 350.00,  'fecha' => '2026-05-22', 'responsable' => 'Marketing'],
            ['categoria' => 'Transporte y viáticos',        'concepto' => 'Viáticos visita a convenio USFA',    'monto' => 280.00,  'fecha' => '2026-05-25', 'responsable' => 'Dirección Académica'],
            ['categoria' => 'Refrigerios y atenciones',     'concepto' => 'Refrigerio reunión de docentes',     'monto' => 320.00,  'fecha' => '2026-05-28', 'responsable' => 'Administración'],
            ['categoria' => 'Software y licencias',         'concepto' => 'Licencia anual Zoom Pro',            'monto' => 1200.00, 'fecha' => '2026-05-03', 'responsable' => 'Soporte Técnico'],
            ['categoria' => 'Software y licencias',         'concepto' => 'Hosting y dominio web',              'monto' => 480.00,  'fecha' => '2026-06-01', 'responsable' => 'Soporte Técnico'],
            ['categoria' => 'Papelería e impresiones',      'concepto' => 'Impresión de certificados',          'monto' => 260.00,  'fecha' => '2026-06-08', 'responsable' => 'Secretaría'],
            ['categoria' => 'Gasto general Diplomados',     'concepto' => 'Material de bienvenida — Diplomado PMI', 'monto' => 540.00, 'fecha' => '2026-06-10', 'responsable' => 'Coordinación Diplomados'],
        ];

        foreach ($gastos as $g) {
            $catId = $categoriaId($g['categoria']);
            if (! $catId) continue;

            Gasto::firstOrCreate(
                ['concepto' => $g['concepto'], 'fecha' => $g['fecha']],
                [
                    'categoria_gasto_id' => $catId,
                    'monto'              => $g['monto'],
                    'responsable'        => $g['responsable'],
                ]
            );
        }
    }
}
