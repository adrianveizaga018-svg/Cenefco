<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebCifraInstitucionalSeeder extends Seeder
{
    public function run(): void
    {
        $cifras = [
            ['valor' => '+5',    'etiqueta' => 'Años de experiencia',    'descripcion' => 'Formando profesionales de calidad', 'icono' => 'fas fa-calendar-alt', 'color' => '#1a56db', 'orden' => 1, 'activo' => true],
            ['valor' => '+500',  'etiqueta' => 'Profesionales formados', 'descripcion' => 'Egresados a nivel nacional',        'icono' => 'fas fa-user-graduate', 'color' => '#0e9f6e', 'orden' => 2, 'activo' => true],
            ['valor' => '+30',   'etiqueta' => 'Programas y cursos',     'descripcion' => 'En distintas áreas del conocimiento', 'icono' => 'fas fa-book-open',    'color' => '#e3a008', 'orden' => 3, 'activo' => true],
            ['valor' => '+20',   'etiqueta' => 'Docentes expertos',      'descripcion' => 'Con amplia trayectoria académica',  'icono' => 'fas fa-chalkboard-teacher', 'color' => '#e02424', 'orden' => 4, 'activo' => true],
        ];

        foreach ($cifras as $cifra) {
            DB::table('web_cifra_institucional')->updateOrInsert(
                ['etiqueta' => $cifra['etiqueta']],
                $cifra
            );
        }
    }
}
