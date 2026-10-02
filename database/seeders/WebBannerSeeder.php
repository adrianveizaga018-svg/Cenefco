<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebBannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'titulo'           => 'Formación Continua de Excelencia',
                'subtitulo'        => 'Programas diseñados para potenciar tu desarrollo profesional',
                'imagen_url'       => 'assets/img/bg/header-img4.png',
                'imagen_alt'       => 'Formación continua CENEFCO',
                'imagen_movil_url' => null,
                'enlace_url'       => '/cursos',
                'enlace_texto'     => 'Ver Programas',
                'enlace_target'    => '_self',
                'posicion'         => 'hero',
                'orden'            => 0,
                'activo'           => true,
                'fecha_inicio'     => null,
                'fecha_fin'        => null,
            ],
            [
                'titulo'           => 'Diplomados y Especializaciones',
                'subtitulo'        => 'Certifícate con los mejores profesionales del área',
                'imagen_url'       => 'assets/img/bg/header-img2.png',
                'imagen_alt'       => 'Diplomados CENEFCO',
                'imagen_movil_url' => null,
                'enlace_url'       => '/cursos',
                'enlace_texto'     => 'Inscríbete Ahora',
                'enlace_target'    => '_self',
                'posicion'         => 'hero',
                'orden'            => 1,
                'activo'           => true,
                'fecha_inicio'     => null,
                'fecha_fin'        => null,
            ],
            [
                'titulo'           => 'Capacitación Técnica y Profesional',
                'subtitulo'        => 'Cursos presenciales y virtuales a tu medida',
                'imagen_url'       => 'assets/img/bg/header-img3.png',
                'imagen_alt'       => 'Capacitación técnica CENEFCO',
                'imagen_movil_url' => null,
                'enlace_url'       => '/contact',
                'enlace_texto'     => 'Contáctanos',
                'enlace_target'    => '_self',
                'posicion'         => 'hero',
                'orden'            => 2,
                'activo'           => true,
                'fecha_inicio'     => null,
                'fecha_fin'        => null,
            ],
        ];

        foreach ($banners as $banner) {
            DB::table('web_banner')->updateOrInsert(
                ['titulo' => $banner['titulo'], 'posicion' => $banner['posicion']],
                $banner
            );
        }

        $this->command->info('✓ Banners del portal creados: '.count($banners));
    }
}
