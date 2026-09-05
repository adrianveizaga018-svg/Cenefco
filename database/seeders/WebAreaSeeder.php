<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebAreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            [
                'titulo'      => 'Derecho y Ciencias Políticas',
                'icono'       => 'lucideScale',
                'color'       => '#1a56db',
                'descripcion' => 'Formación en derecho, justicia, política y gestión pública.',
                'orden'       => 1,
            ],
            [
                'titulo'      => 'Ingeniería y Tecnología',
                'icono'       => 'lucideCpu',
                'color'       => '#0e9f6e',
                'descripcion' => 'Programas orientados a la innovación, ingeniería y desarrollo tecnológico.',
                'orden'       => 2,
            ],
            [
                'titulo'      => 'Ciencias Empresariales',
                'icono'       => 'lucideBriefcaseBusiness',
                'color'       => '#e3a008',
                'descripcion' => 'Gestión empresarial, administración, finanzas y emprendimiento.',
                'orden'       => 3,
            ],
            [
                'titulo'      => 'Arquitectura y Diseño',
                'icono'       => 'lucideCompass',
                'color'       => '#e02424',
                'descripcion' => 'Diseño arquitectónico, urbanismo y disciplinas creativas.',
                'orden'       => 4,
            ],
            [
                'titulo'      => 'Ciencias Humanas y Sociales',
                'icono'       => 'lucideUsers',
                'color'       => '#7e3af2',
                'descripcion' => 'Educación, psicología, sociología y ciencias del comportamiento humano.',
                'orden'       => 5,
            ],
        ];

        foreach ($areas as $area) {
            $slug = Str::slug($area['titulo']);
            DB::table('web_area')->updateOrInsert(
                ['slug' => $slug],
                array_merge($area, [
                    'slug'             => $slug,
                    'logo_url'         => null,
                    'logo_alt'         => null,
                    'galeria'          => json_encode([]),
                    'activo'           => true,
                    'meta_titulo'      => null,
                    'meta_descripcion' => null,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ])
            );
        }
    }
}
