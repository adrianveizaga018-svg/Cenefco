<?php

namespace Database\Seeders;

use App\Infrastructure\Gastos\Models\CategoriaGasto;
use Illuminate\Database\Seeder;

class CategoriaGastoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Alquiler de oficina',          'linea_negocio' => null],
            ['nombre' => 'Servicios básicos (luz/agua)', 'linea_negocio' => null],
            ['nombre' => 'Internet y telefonía',         'linea_negocio' => null],
            ['nombre' => 'Material de escritorio',       'linea_negocio' => null],
            ['nombre' => 'Mantenimiento de equipos',     'linea_negocio' => null],
            ['nombre' => 'Publicidad y marketing',       'linea_negocio' => null],
            ['nombre' => 'Transporte y viáticos',        'linea_negocio' => null],
            ['nombre' => 'Refrigerios y atenciones',     'linea_negocio' => null],
            ['nombre' => 'Software y licencias',         'linea_negocio' => null],
            ['nombre' => 'Papelería e impresiones',      'linea_negocio' => null],
            ['nombre' => 'Sueldos',                       'linea_negocio' => null],
            ['nombre' => 'Gasto general Diplomados',      'linea_negocio' => 'diplomados'],
            ['nombre' => 'Gasto general Cursos R.M.',     'linea_negocio' => 'cursos_rm'],
            ['nombre' => 'Gasto general Cursos con aval', 'linea_negocio' => 'cursos_aval'],
            ['nombre' => 'ICALP',                          'linea_negocio' => 'cursos_aval'],
            ['nombre' => 'Colegio de Economistas',         'linea_negocio' => 'cursos_aval'],
            ['nombre' => 'Regalía Sociedad de Ingenieros',  'linea_negocio' => 'cursos_aval'],
        ];

        foreach ($categorias as $categoria) {
            CategoriaGasto::firstOrCreate(
                ['nombre' => $categoria['nombre']],
                ['linea_negocio' => $categoria['linea_negocio'], 'activo' => true]
            );
        }
    }
}
