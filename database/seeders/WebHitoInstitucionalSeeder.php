<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebHitoInstitucionalSeeder extends Seeder
{
    public function run(): void
    {
        $hitos = [
            ['anio' => '2020', 'titulo' => 'Fundación institucional',       'descripcion' => 'Inicio de actividades con los primeros programas de formación.', 'orden' => 1],
            ['anio' => '2021', 'titulo' => 'Primera promoción de egresados', 'descripcion' => 'Primera generación de profesionales formados por la institución.', 'orden' => 2],
            ['anio' => '2022', 'titulo' => 'Expansión de programas',        'descripcion' => 'Ampliación de la oferta académica con nuevas especializaciones.', 'orden' => 3],
            ['anio' => '2023', 'titulo' => 'Plataforma virtual',            'descripcion' => 'Lanzamiento del campus virtual para modalidad online.',           'orden' => 4],
            ['anio' => '2024', 'titulo' => 'Certificados digitales',        'descripcion' => 'Implementación del sistema de certificados con verificación QR.',  'orden' => 5],
        ];

        foreach ($hitos as $hito) {
            DB::table('web_hito_institucional')->updateOrInsert(
                ['anio' => $hito['anio'], 'titulo' => $hito['titulo']],
                array_merge($hito, ['imagen_url' => null, 'imagen_alt' => null, 'activo' => true])
            );
        }
    }
}
