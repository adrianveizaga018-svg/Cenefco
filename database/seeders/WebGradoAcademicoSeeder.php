<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebGradoAcademicoSeeder extends Seeder
{
    public function run(): void
    {
        $grados = [
            ['nombre' => 'Bachiller',           'abreviatura' => 'Bach.',    'requiere_titulo' => false, 'activo' => true, 'orden' => 1],
            ['nombre' => 'Técnico Superior',    'abreviatura' => 'T.S.',     'requiere_titulo' => true,  'activo' => true, 'orden' => 2],
            ['nombre' => 'Licenciado/a',        'abreviatura' => 'Lic.',     'requiere_titulo' => true,  'activo' => true, 'orden' => 3],
            ['nombre' => 'Ingeniero/a',         'abreviatura' => 'Ing.',     'requiere_titulo' => true,  'activo' => true, 'orden' => 4],
            ['nombre' => 'Médico/a',            'abreviatura' => 'Dr./Dra.', 'requiere_titulo' => true,  'activo' => true, 'orden' => 5],
            ['nombre' => 'Magíster / Máster',   'abreviatura' => 'Mg.',      'requiere_titulo' => true,  'activo' => true, 'orden' => 6],
            ['nombre' => 'Doctor/a (PhD)',       'abreviatura' => 'PhD.',     'requiere_titulo' => true,  'activo' => true, 'orden' => 7],
            ['nombre' => 'Sin grado académico', 'abreviatura' => '',         'requiere_titulo' => false, 'activo' => true, 'orden' => 8],
        ];

        foreach ($grados as $grado) {
            DB::table('web_grado_academico')->updateOrInsert(
                ['nombre' => $grado['nombre']],
                $grado
            );
        }
    }
}
