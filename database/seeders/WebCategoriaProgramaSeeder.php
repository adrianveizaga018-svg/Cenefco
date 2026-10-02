<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebCategoriaProgramaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Diplomado',           'icono' => 'fas fa-graduation-cap', 'color' => '#1a56db', 'orden' => 1],
            ['nombre' => 'Curso',               'icono' => 'fas fa-book',           'color' => '#0e9f6e', 'orden' => 2],
            ['nombre' => 'Taller',              'icono' => 'fas fa-tools',          'color' => '#e3a008', 'orden' => 3],
            ['nombre' => 'Maestría',            'icono' => 'fas fa-university',     'color' => '#e02424', 'orden' => 4],
            ['nombre' => 'Especialización',     'icono' => 'fas fa-microscope',     'color' => '#7e3af2', 'orden' => 5],
            ['nombre' => 'Seminario',           'icono' => 'fas fa-comments',       'color' => '#057a55', 'orden' => 6],
            ['nombre' => 'Certificación',       'icono' => 'fas fa-certificate',    'color' => '#c27803', 'orden' => 7],
        ];

        foreach ($categorias as $cat) {
            $slug = Str::slug($cat['nombre']);
            DB::table('web_categoria_programa')->updateOrInsert(
                ['slug' => $slug],
                array_merge($cat, [
                    'slug' => $slug,
                    'descripcion' => null,
                    'imagen_url' => null,
                    'imagen_alt' => null,
                    'activo' => true,
                    'meta_titulo' => null,
                    'meta_descripcion' => null,
                ])
            );
        }
    }
}
