<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriasNoticiaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Institucional',      'color_hex' => '#1a56db'],
            ['nombre' => 'Académico',           'color_hex' => '#0e9f6e'],
            ['nombre' => 'Eventos',             'color_hex' => '#7e3af2'],
            ['nombre' => 'Convocatorias',       'color_hex' => '#e3a008'],
            ['nombre' => 'Comunicados',         'color_hex' => '#e02424'],
            ['nombre' => 'Investigación',       'color_hex' => '#057a55'],
        ];

        foreach ($categorias as $cat) {
            $slug = Str::slug($cat['nombre']);
            DB::table('categorias_noticia')->updateOrInsert(
                ['slug' => $slug],
                ['nombre' => $cat['nombre'], 'slug' => $slug, 'color_hex' => $cat['color_hex'], 'activa' => true]
            );
        }
    }
}
