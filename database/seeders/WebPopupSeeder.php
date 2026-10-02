<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebPopupSeeder extends Seeder
{
    public function run(): void
    {
        $popups = [
            [
                'titulo'                  => '¡Inscripciones Abiertas 2026!',
                'contenido'               => 'Aprovecha nuestros diplomados y cursos de formación continua. Plazas limitadas — ¡asegura tu cupo ahora!',
                'imagen_url'              => null,
                'enlace_url'              => '/cursos',
                'enlace_texto'            => 'Ver Programas',
                'posicion'                => 'center',
                'delay_segundos'          => 3,
                'mostrar_una_vez_sesion'  => true,
                'mostrar_una_vez_siempre' => false,
                'paginas_mostrar'         => '/',
                'activo'                  => true,
                'fecha_inicio'            => null,
                'fecha_fin'               => null,
            ],
            [
                'titulo'                  => 'Descarga nuestro Brochure Gratuito',
                'contenido'               => 'Conoce toda nuestra oferta académica 2026. Descarga el brochure institucional sin costo y compártelo.',
                'imagen_url'              => null,
                'enlace_url'              => '/descargables',
                'enlace_texto'            => 'Descargar Ahora',
                'posicion'                => 'bottom-right',
                'delay_segundos'          => 5,
                'mostrar_una_vez_sesion'  => false,
                'mostrar_una_vez_siempre' => true,
                'paginas_mostrar'         => null,
                'activo'                  => false,
                'fecha_inicio'            => null,
                'fecha_fin'               => null,
            ],
        ];

        foreach ($popups as $popup) {
            DB::table('web_popup')->updateOrInsert(
                ['titulo' => $popup['titulo']],
                $popup
            );
        }

        $this->command->info('✓ Popups creados: ' . count($popups));
    }
}
