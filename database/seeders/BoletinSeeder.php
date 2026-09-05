<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoletinSeeder extends Seeder
{
    public function run(): void
    {
        $boletines = [
            [
                'id_boletin'          => 1,
                'id_us_reg'           => 0,
                'num_boletin'         => 1,
                'titulo_boletin'      => 'Boletín Institucional N° 1 - Inicio de Gestión 2025',
                'titulo_pagina'       => 'Boletín N° 1 | CENEFCO',
                'descripcion_boletin' => 'Presentamos el primer boletín institucional del año 2025, con información sobre nuestros nuevos programas de formación continua, logros académicos y actividades destacadas del periodo.',
                'estado'              => 1,
                'fecha_reg'           => now(),
                'imagen_url'          => null,
            ],
            [
                'id_boletin'          => 2,
                'id_us_reg'           => 0,
                'num_boletin'         => 2,
                'titulo_boletin'      => 'Boletín Institucional N° 2 - Avances Académicos',
                'titulo_pagina'       => 'Boletín N° 2 | CENEFCO',
                'descripcion_boletin' => 'En este segundo número compartimos los resultados del primer semestre: número de graduados, nuevas alianzas estratégicas y la apertura de programas en nuevas modalidades.',
                'estado'              => 1,
                'fecha_reg'           => now(),
                'imagen_url'          => null,
            ],
            [
                'id_boletin'          => 3,
                'id_us_reg'           => 0,
                'num_boletin'         => 3,
                'titulo_boletin'      => 'Boletín Institucional N° 3 - Innovación Educativa',
                'titulo_pagina'       => 'Boletín N° 3 | CENEFCO',
                'descripcion_boletin' => 'Este número está dedicado a la innovación en la educación continua: plataformas digitales, nuevas metodologías de enseñanza y testimonios de nuestros egresados.',
                'estado'              => 1,
                'fecha_reg'           => now(),
                'imagen_url'          => null,
            ],
            [
                'id_boletin'          => 4,
                'id_us_reg'           => 0,
                'num_boletin'         => 4,
                'titulo_boletin'      => 'Boletín Institucional N° 4 - Cierre de Gestión 2025',
                'titulo_pagina'       => 'Boletín N° 4 | CENEFCO',
                'descripcion_boletin' => 'Boletín de cierre de gestión con el resumen anual de actividades, estadísticas de inscripciones, programas ejecutados y perspectivas para la gestión 2026.',
                'estado'              => 0,
                'fecha_reg'           => now(),
                'imagen_url'          => null,
            ],
        ];

        foreach ($boletines as $boletin) {
            DB::table('t_boletin')->updateOrInsert(
                ['id_boletin' => $boletin['id_boletin'], 'id_us_reg' => $boletin['id_us_reg']],
                $boletin
            );
        }
    }
}
