<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebEtiquetaSeeder extends Seeder
{
    public function run(): void
    {
        $etiquetas = [
            ['nombre' => 'Destacado',       'color' => '#e3a008'],
            ['nombre' => 'Nuevo',           'color' => '#0e9f6e'],
            ['nombre' => 'Online',          'color' => '#1a56db'],
            ['nombre' => 'Presencial',      'color' => '#7e3af2'],
            ['nombre' => 'Semipresencial',  'color' => '#057a55'],
            ['nombre' => 'Gratuito',        'color' => '#e02424'],
            ['nombre' => 'Con certificado', 'color' => '#c27803'],
            ['nombre' => 'Cupos limitados', 'color' => '#9f580a'],
        ];

        foreach ($etiquetas as $etiqueta) {
            $slug = Str::slug($etiqueta['nombre']);
            DB::table('web_etiqueta')->updateOrInsert(
                ['slug' => $slug],
                ['nombre' => $etiqueta['nombre'], 'slug' => $slug, 'color' => $etiqueta['color']]
            );
        }
    }
}
