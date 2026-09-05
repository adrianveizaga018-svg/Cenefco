<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TesisSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        $tesis = [
            [
                'id_tesis'          => 1,
                'num_tesis'         => 1,
                'titulo_tesis'      => 'Gestión por competencias en instituciones de educación continua: un enfoque estratégico',
                'descripcion_tesis' => 'La presente tesis analiza los modelos de gestión por competencias aplicados en centros de formación continua de Bolivia, evaluando su impacto en la calidad educativa y la empleabilidad de los egresados.',
                'fecha_publicacion' => '2024-06-15',
                'autor'             => 'María Fernanda Quispe Mamani',
                'tipo_tesis'        => 1,
                'archivo'           => null,
            ],
            [
                'id_tesis'          => 2,
                'num_tesis'         => 2,
                'titulo_tesis'      => 'Implementación de metodologías activas en programas de posgrado semipresencial',
                'descripcion_tesis' => 'Estudio sobre la efectividad del aprendizaje basado en proyectos (ABP) y el aula invertida en programas de diplomado y especialización impartidos en modalidad semipresencial en Bolivia.',
                'fecha_publicacion' => '2024-09-20',
                'autor'             => 'Carlos Alejandro Torrez Villca',
                'tipo_tesis'        => 1,
                'archivo'           => null,
            ],
            [
                'id_tesis'          => 3,
                'num_tesis'         => 3,
                'titulo_tesis'      => 'Evaluación del impacto socioeconómico de los programas de formación técnica en el departamento de La Paz',
                'descripcion_tesis' => 'Análisis cuantitativo y cualitativo del impacto de la formación técnica y profesional en la inserción laboral y el nivel de ingresos de los participantes de programas de capacitación en el departamento de La Paz durante el periodo 2020-2024.',
                'fecha_publicacion' => '2025-02-10',
                'autor'             => 'Ana Lucía Condori Apaza',
                'tipo_tesis'        => 2,
                'archivo'           => null,
            ],
            [
                'id_tesis'          => 4,
                'num_tesis'         => 4,
                'titulo_tesis'      => 'Diseño curricular basado en competencias laborales para el sector salud en Bolivia',
                'descripcion_tesis' => 'Propuesta de un modelo de diseño curricular alineado a los estándares del Ministerio de Salud y a las necesidades reales del mercado laboral sanitario boliviano, con énfasis en competencias clínicas y blandas.',
                'fecha_publicacion' => '2025-04-05',
                'autor'             => 'Roberto Enrique Mamani Choque',
                'tipo_tesis'        => 1,
                'archivo'           => null,
            ],
            [
                'id_tesis'          => 5,
                'num_tesis'         => 5,
                'titulo_tesis'      => 'Plataformas de e-learning y su rol en la democratización del acceso a la educación superior continua',
                'descripcion_tesis' => 'Investigación sobre el uso de plataformas LMS (Moodle, Canvas) en instituciones bolivianas de formación continua y su incidencia en la reducción de barreras geográficas y económicas para el acceso a la educación superior.',
                'fecha_publicacion' => '2025-07-18',
                'autor'             => 'Daniela Paola Flores Gutierrez',
                'tipo_tesis'        => 2,
                'archivo'           => null,
            ],
            [
                'id_tesis'          => 6,
                'num_tesis'         => 6,
                'titulo_tesis'      => 'Factores determinantes en la retención estudiantil en programas de diplomado a distancia',
                'descripcion_tesis' => 'Identificación y análisis de los factores académicos, tecnológicos y motivacionales que influyen en la tasa de retención y culminación de programas de diplomado en modalidad virtual en el contexto boliviano post-pandemia.',
                'fecha_publicacion' => '2024-11-30',
                'autor'             => 'Luis Fernando Chávez Rojas',
                'tipo_tesis'        => 1,
                'archivo'           => null,
            ],
        ];

        foreach ($tesis as $t) {
            if (DB::table('t_tesis')->where('id_tesis', $t['id_tesis'])->where('id_us_reg', 1)->exists()) {
                
                if (! DB::table('t_tesis')->where('id_tesis', $t['id_tesis'])->whereNotNull('slug')->exists()) {
                    DB::table('t_tesis')->where('id_tesis', $t['id_tesis'])->update(['slug' => Str::slug($t['titulo_tesis'])]);
                }
                continue;
            }
            DB::table('t_tesis')->insertOrIgnore(array_merge($t, [
                'slug'          => Str::slug($t['titulo_tesis']),
                'id_us_reg'     => 1,
                'estado'        => 1,
                'fecha_reg'     => $now,
                'per_modificar' => 0,
            ]));
        }

        $this->command->info('✓ ' . count($tesis) . ' tesis insertadas en t_tesis.');
    }
}
