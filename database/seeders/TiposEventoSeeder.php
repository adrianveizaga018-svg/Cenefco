<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TiposEventoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Conferencia',     'color_hex' => '#1a56db'],
            ['nombre' => 'Taller',          'color_hex' => '#0e9f6e'],
            ['nombre' => 'Seminario',       'color_hex' => '#7e3af2'],
            ['nombre' => 'Feria',           'color_hex' => '#e3a008'],
            ['nombre' => 'Graduación',      'color_hex' => '#057a55'],
            ['nombre' => 'Congreso',        'color_hex' => '#e02424'],
            ['nombre' => 'Webinar',         'color_hex' => '#3f83f8'],
            ['nombre' => 'Inauguración',    'color_hex' => '#c27803'],
        ];

        foreach ($tipos as $tipo) {
            $slug = Str::slug($tipo['nombre']);
            DB::table('tipos_evento')->updateOrInsert(
                ['slug' => $slug],
                ['nombre' => $tipo['nombre'], 'slug' => $slug, 'color_hex' => $tipo['color_hex'], 'activo' => true]
            );
        }
    }
}
