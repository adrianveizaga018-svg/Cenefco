<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TiposNormaSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Reglamento',          'sigla' => 'REGL'],
            ['nombre' => 'Resolución',           'sigla' => 'RES'],
            ['nombre' => 'Disposición',          'sigla' => 'DISP'],
            ['nombre' => 'Decreto',              'sigla' => 'DEC'],
            ['nombre' => 'Instructivo',          'sigla' => 'INST'],
            ['nombre' => 'Circular',             'sigla' => 'CIRC'],
            ['nombre' => 'Convocatoria',         'sigla' => 'CONV'],
        ];

        foreach ($tipos as $tipo) {
            $slug = Str::slug($tipo['nombre']);
            DB::table('tipos_norma')->updateOrInsert(
                ['slug' => $slug],
                ['nombre' => $tipo['nombre'], 'sigla' => $tipo['sigla'], 'slug' => $slug, 'activo' => true]
            );
        }
    }
}
