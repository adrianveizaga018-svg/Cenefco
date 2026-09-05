<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebGaleriaVideoSeeder extends Seeder
{
    public function run(): void
    {
        $videos = [
            [
                'titulo'       => 'Introducción a la Gestión de Proyectos',
                'descripcion'  => 'Aprende los fundamentos de la gestión de proyectos con metodologías modernas aplicadas al entorno profesional.',
                'plataforma'   => 'youtube',
                'url_video'    => 'https://www.youtube.com/watch?v=GGU1P6lBc00',
                'video_id'     => 'GGU1P6lBc00',
                'miniatura_url'=> 'https://img.youtube.com/vi/GGU1P6lBc00/hqdefault.jpg',
                'duracion'     => '12:34',
                'tipo'         => 'institucional',
                'programa_id'  => null,
                'destacado'    => true,
                'orden'        => 0,
                'vistas'       => 0,
                'activo'       => true,
            ],
            [
                'titulo'       => 'Diplomado en Contabilidad y Finanzas — Presentación',
                'descripcion'  => 'Conoce el programa, los módulos, docentes y requisitos de nuestro Diplomado en Contabilidad y Finanzas.',
                'plataforma'   => 'youtube',
                'url_video'    => 'https://www.youtube.com/watch?v=HQ0cVlkzc6I',
                'video_id'     => 'HQ0cVlkzc6I',
                'miniatura_url'=> 'https://img.youtube.com/vi/HQ0cVlkzc6I/hqdefault.jpg',
                'duracion'     => '8:20',
                'tipo'         => 'programa',
                'programa_id'  => null,
                'destacado'    => true,
                'orden'        => 1,
                'vistas'       => 0,
                'activo'       => true,
            ],
            [
                'titulo'       => 'Curso de Liderazgo y Trabajo en Equipo',
                'descripcion'  => 'Desarrolla habilidades de liderazgo efectivo y aprende a potenciar el trabajo colaborativo en tu organización.',
                'plataforma'   => 'youtube',
                'url_video'    => 'https://www.youtube.com/watch?v=f-Ei_B25W9o',
                'video_id'     => 'f-Ei_B25W9o',
                'miniatura_url'=> 'https://img.youtube.com/vi/f-Ei_B25W9o/hqdefault.jpg',
                'duracion'     => '15:47',
                'tipo'         => 'programa',
                'programa_id'  => null,
                'destacado'    => false,
                'orden'        => 2,
                'vistas'       => 0,
                'activo'       => true,
            ],
            [
                'titulo'       => 'Bienvenida y Presentación Institucional CENEFCO',
                'descripcion'  => 'Mensaje de bienvenida de las autoridades institucionales y presentación de la oferta académica del centro.',
                'plataforma'   => 'youtube',
                'url_video'    => 'https://www.youtube.com/watch?v=aKPTFJn_A5I',
                'video_id'     => 'aKPTFJn_A5I',
                'miniatura_url'=> 'https://img.youtube.com/vi/aKPTFJn_A5I/hqdefault.jpg',
                'duracion'     => '5:00',
                'tipo'         => 'institucional',
                'programa_id'  => null,
                'destacado'    => true,
                'orden'        => 3,
                'vistas'       => 0,
                'activo'       => true,
            ],
            [
                'titulo'       => 'Metodología de Investigación Científica',
                'descripcion'  => 'Sesión introductoria sobre técnicas y herramientas para la investigación aplicada en ciencias sociales y administrativas.',
                'plataforma'   => 'youtube',
                'url_video'    => 'https://www.youtube.com/watch?v=lxYdpWaILog',
                'video_id'     => 'lxYdpWaILog',
                'miniatura_url'=> 'https://img.youtube.com/vi/lxYdpWaILog/hqdefault.jpg',
                'duracion'     => '22:10',
                'tipo'         => 'clase',
                'programa_id'  => null,
                'destacado'    => false,
                'orden'        => 4,
                'vistas'       => 0,
                'activo'       => true,
            ],
            [
                'titulo'       => 'Transformación Digital en las Organizaciones',
                'descripcion'  => 'Cómo las empresas están adoptando herramientas digitales para mejorar su competitividad y eficiencia operativa.',
                'plataforma'   => 'youtube',
                'url_video'    => 'https://www.youtube.com/watch?v=BMLEb4cDXFk',
                'video_id'     => 'BMLEb4cDXFk',
                'miniatura_url'=> 'https://img.youtube.com/vi/BMLEb4cDXFk/hqdefault.jpg',
                'duracion'     => '18:55',
                'tipo'         => 'clase',
                'programa_id'  => null,
                'destacado'    => false,
                'orden'        => 5,
                'vistas'       => 0,
                'activo'       => true,
            ],
        ];

        foreach ($videos as $video) {
            DB::table('web_galeria_video')->updateOrInsert(
                ['titulo' => $video['titulo']],
                $video
            );
        }

        $this->command->info('✓ Videos de galería creados: ' . count($videos));
    }
}
