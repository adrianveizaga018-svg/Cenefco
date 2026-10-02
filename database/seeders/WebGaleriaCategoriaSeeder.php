<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebGaleriaCategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Graduaciones',        'orden' => 1],
            ['nombre' => 'Eventos académicos',  'orden' => 2],
            ['nombre' => 'Instalaciones',       'orden' => 3],
            ['nombre' => 'Actividades',         'orden' => 4],
        ];

        foreach ($categorias as $cat) {
            $slug = Str::slug($cat['nombre']);
            DB::table('web_galeria_categoria')->updateOrInsert(
                ['slug' => $slug],
                ['nombre' => $cat['nombre'], 'slug' => $slug, 'descripcion' => null, 'orden' => $cat['orden'], 'activo' => true]
            );
        }
    }
}
