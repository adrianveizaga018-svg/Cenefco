<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoriaInstitucionalSeeder extends Seeder
{
    public function run(): void
    {
        $hitos = [
            [
                'titulo'      => 'Fundación de CENEFCO',
                'contenido'   => 'CENEFCO fue fundado con el objetivo de brindar formación continua y especializada de calidad a profesionales y estudiantes de la región.',
                'fecha_inicio' => 'Año de fundación',
                'orden'       => 1,
                'activo'      => true,
            ],
            [
                'titulo'      => 'Primera promoción de egresados',
                'contenido'   => 'Se graduó la primera cohorte de estudiantes de diplomados y cursos de especialización, consolidando el modelo educativo institucional.',
                'orden'       => 2,
                'activo'      => true,
            ],
            [
                'titulo'      => 'Expansión de la oferta académica',
                'contenido'   => 'Se amplió la oferta con nuevos programas de postgrado, diplomados virtuales y semipresenciales para atender mayor demanda regional.',
                'orden'       => 3,
                'activo'      => true,
            ],
            [
                'titulo'      => 'Plataforma digital institucional',
                'contenido'   => 'Implementación del sistema de gestión académica y del campus virtual, permitiendo la inscripción y seguimiento en línea de todos los programas.',
                'orden'       => 4,
                'activo'      => true,
            ],
        ];

        foreach ($hitos as $hito) {
            $exists = DB::table('historia_municipio')->where('titulo', $hito['titulo'])->exists();
            if (! $exists) {
                DB::table('historia_municipio')->insertOrIgnore($hito);
            }
        }
    }
}
