<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaArticuloSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['id_cat_art' => 1, 'nombre_cat' => 'Formación y Capacitación', 'sub_cat' => null],
            ['id_cat_art' => 2, 'nombre_cat' => 'Gestión Pública',          'sub_cat' => null],
            ['id_cat_art' => 3, 'nombre_cat' => 'Tecnología y Digitalización', 'sub_cat' => null],
            ['id_cat_art' => 4, 'nombre_cat' => 'Noticias Institucionales',  'sub_cat' => null],
            ['id_cat_art' => 5, 'nombre_cat' => 'Recursos Humanos',          'sub_cat' => null],
            
            ['id_cat_art' => 6, 'nombre_cat' => 'Diplomados',     'sub_cat' => 1],
            ['id_cat_art' => 7, 'nombre_cat' => 'Cursos Cortos',  'sub_cat' => 1],
            ['id_cat_art' => 8, 'nombre_cat' => 'Posgrados',      'sub_cat' => 1],
            
            ['id_cat_art' => 9,  'nombre_cat' => 'Presupuesto Público',   'sub_cat' => 2],
            ['id_cat_art' => 10, 'nombre_cat' => 'Contratación Pública',  'sub_cat' => 2],
            ['id_cat_art' => 11, 'nombre_cat' => 'Transparencia',         'sub_cat' => 2],
        ];

        foreach ($categorias as $cat) {
            $exists = DB::table('t_categoria_articulo')
                ->where('id_cat_art', $cat['id_cat_art'])
                ->where('id_us_reg', 0)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('t_categoria_articulo')->insertOrIgnore([
                'id_cat_art'    => $cat['id_cat_art'],
                'id_us_reg'     => 0,
                'num_cat'       => 0,
                'nombre_cat'    => $cat['nombre_cat'],
                'sub_cat'       => $cat['sub_cat'],
                'estado'        => 1,
                'fecha_reg'     => now()->toDateTimeString(),
                'per_modificar' => 0,
            ]);
        }
    }
}
