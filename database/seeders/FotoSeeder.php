<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FotoSeeder extends Seeder
{
    public function run(): void
    {
        $fotos = [
            [
                'id_foto'             => 1,
                'id_us_reg'           => 1,
                'num_foto'            => 1,
                'titulo_foto'         => 'Ceremonia de Graduación — Diplomado Gestión Empresarial 2024',
                'descripcion_foto'    => 'Graduados del Diplomado en Gestión Empresarial, edición 2024, recibiendo sus certificados en el Salón de Actos del CENEFCO.',
                'foto'                => null,
                'alt'                 => 'Ceremonia de graduación CENEFCO 2024',
                'orden'               => 1,
                'destacada'           => 1,
                'galeria_categoria_id'=> 1,
                'fecha_foto'          => '2024-11-20',
                'estado'              => 1,
                'fecha_reg'           => now(),
                'per_modificar'       => 0,
            ],
            [
                'id_foto'             => 2,
                'id_us_reg'           => 1,
                'num_foto'            => 2,
                'titulo_foto'         => 'Conferencia Internacional de Formación Continua 2025',
                'descripcion_foto'    => 'Ponentes internacionales y asistentes durante la conferencia anual organizada por el CENEFCO en La Paz.',
                'foto'                => null,
                'alt'                 => 'Conferencia Internacional CENEFCO 2025',
                'orden'               => 2,
                'destacada'           => 1,
                'galeria_categoria_id'=> 2,
                'fecha_foto'          => '2025-08-15',
                'estado'              => 1,
                'fecha_reg'           => now(),
                'per_modificar'       => 0,
            ],
            [
                'id_foto'             => 3,
                'id_us_reg'           => 1,
                'num_foto'            => 3,
                'titulo_foto'         => 'Taller de Metodologías Activas — Aula Virtual',
                'descripcion_foto'    => 'Participantes del taller de metodologías activas aplicando técnicas de aprendizaje basado en proyectos.',
                'foto'                => null,
                'alt'                 => 'Taller metodologías activas CENEFCO',
                'orden'               => 3,
                'destacada'           => 0,
                'galeria_categoria_id'=> 2,
                'fecha_foto'          => '2025-09-05',
                'estado'              => 1,
                'fecha_reg'           => now(),
                'per_modificar'       => 0,
            ],
            [
                'id_foto'             => 4,
                'id_us_reg'           => 1,
                'num_foto'            => 4,
                'titulo_foto'         => 'Instalaciones del Centro CENEFCO — Aulas',
                'descripcion_foto'    => 'Vista de las modernas aulas equipadas del CENEFCO, diseñadas para una experiencia de aprendizaje óptima.',
                'foto'                => null,
                'alt'                 => 'Aulas CENEFCO',
                'orden'               => 4,
                'destacada'           => 0,
                'galeria_categoria_id'=> 3,
                'fecha_foto'          => '2025-01-10',
                'estado'              => 1,
                'fecha_reg'           => now(),
                'per_modificar'       => 0,
            ],
            [
                'id_foto'             => 5,
                'id_us_reg'           => 1,
                'num_foto'            => 5,
                'titulo_foto'         => 'Firma de Convenio con Universidad Tecnológica',
                'descripcion_foto'    => 'Autoridades del CENEFCO y representantes de la UTB durante la firma del convenio de cooperación interinstitucional.',
                'foto'                => null,
                'alt'                 => 'Convenio CENEFCO UTB',
                'orden'               => 5,
                'destacada'           => 1,
                'galeria_categoria_id'=> 4,
                'fecha_foto'          => '2025-05-20',
                'estado'              => 1,
                'fecha_reg'           => now(),
                'per_modificar'       => 0,
            ],
            [
                'id_foto'             => 6,
                'id_us_reg'           => 1,
                'num_foto'            => 6,
                'titulo_foto'         => 'Seminario de Gestión del Talento Humano',
                'descripcion_foto'    => 'Participantes y expositores del seminario intensivo sobre gestión del talento humano en organizaciones bolivianas.',
                'foto'                => null,
                'alt'                 => 'Seminario talento humano CENEFCO',
                'orden'               => 6,
                'destacada'           => 0,
                'galeria_categoria_id'=> 2,
                'fecha_foto'          => '2025-10-10',
                'estado'              => 1,
                'fecha_reg'           => now(),
                'per_modificar'       => 0,
            ],
        ];

        foreach ($fotos as $foto) {
            DB::table('t_foto')->updateOrInsert(
                ['id_foto' => $foto['id_foto'], 'id_us_reg' => $foto['id_us_reg']],
                $foto
            );
        }
    }
}
