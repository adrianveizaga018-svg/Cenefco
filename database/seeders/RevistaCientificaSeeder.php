<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RevistaCientificaSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        $revistas = [
            [
                'id_revistacientifica'          => 1,
                'num_revistacientifica'         => 1,
                'titulo_revistacientifica'      => 'Revista de Innovación Educativa CENEFCO — Año 1, Nº 1',
                'descripcion_revistacientifica' => 'Primera edición de la revista científica del CENEFCO. Publica investigaciones originales sobre innovación pedagógica, tecnología educativa y evaluación de competencias. Arbitrada por pares con revisión doble ciego. ISSN en trámite.',
                'fecha_publicacion'             => '2024-04-30',
                'archivo'                       => null,
            ],
            [
                'id_revistacientifica'          => 2,
                'num_revistacientifica'         => 2,
                'titulo_revistacientifica'      => 'Revista de Innovación Educativa CENEFCO — Año 1, Nº 2',
                'descripcion_revistacientifica' => 'Segunda edición con trabajos de investigación sobre: modelos de evaluación del aprendizaje en entornos virtuales, eficacia de la mentoría académica en programas de posgrado y análisis comparativo de sistemas de acreditación en Latinoamérica.',
                'fecha_publicacion'             => '2024-10-15',
                'archivo'                       => null,
            ],
            [
                'id_revistacientifica'          => 3,
                'num_revistacientifica'         => 3,
                'titulo_revistacientifica'      => 'Revista de Innovación Educativa CENEFCO — Año 2, Nº 1',
                'descripcion_revistacientifica' => 'Primera edición del segundo año. Número temático dedicado a la inteligencia artificial en educación. Artículos sobre: sistemas de tutoría inteligente, detección automatizada de plagio, personalización del aprendizaje y ética de la IA en contextos educativos bolivianos.',
                'fecha_publicacion'             => '2025-04-20',
                'archivo'                       => null,
            ],
            [
                'id_revistacientifica'          => 4,
                'num_revistacientifica'         => 4,
                'titulo_revistacientifica'      => 'Cuadernos de Investigación Aplicada CENEFCO — Nº 1',
                'descripcion_revistacientifica' => 'Serie de cuadernos monográficos de investigación aplicada. Este primer número reúne los mejores trabajos de graduación del diplomado en Gestión Educativa, presentando propuestas de mejora institucional validadas en entornos educativos reales.',
                'fecha_publicacion'             => '2024-06-25',
                'archivo'                       => null,
            ],
            [
                'id_revistacientifica'          => 5,
                'num_revistacientifica'         => 5,
                'titulo_revistacientifica'      => 'Cuadernos de Investigación Aplicada CENEFCO — Nº 2: Salud y Formación',
                'descripcion_revistacientifica' => 'Segundo número temático enfocado en la formación continua del sector salud. Incluye investigaciones sobre simulación clínica, competencias de enfermería, gestión hospitalaria y educación médica continua en el sistema de salud boliviano.',
                'fecha_publicacion'             => '2024-12-02',
                'archivo'                       => null,
            ],
            [
                'id_revistacientifica'          => 6,
                'num_revistacientifica'         => 6,
                'titulo_revistacientifica'      => 'Cuadernos de Investigación Aplicada CENEFCO — Nº 3: Gestión Pública',
                'descripcion_revistacientifica' => 'Tercer número dedicado a investigaciones sobre gestión pública y administración del Estado. Reúne estudios sobre modernización del servicio civil, gobierno electrónico, transparencia institucional y formación de funcionarios públicos en Bolivia.',
                'fecha_publicacion'             => '2025-03-28',
                'archivo'                       => null,
            ],
        ];

        foreach ($revistas as $r) {
            if (DB::table('t_revistacientifica')->where('id_revistacientifica', $r['id_revistacientifica'])->where('id_us_reg', 1)->exists()) {
                if (! DB::table('t_revistacientifica')->where('id_revistacientifica', $r['id_revistacientifica'])->whereNotNull('slug')->exists()) {
                    DB::table('t_revistacientifica')->where('id_revistacientifica', $r['id_revistacientifica'])->update(['slug' => Str::slug($r['titulo_revistacientifica'])]);
                }
                continue;
            }
            DB::table('t_revistacientifica')->insertOrIgnore(array_merge($r, [
                'slug'          => Str::slug($r['titulo_revistacientifica']),
                'id_us_reg'     => 1,
                'estado'        => 1,
                'fecha_reg'     => $now,
                'per_modificar' => 0,
            ]));
        }

        $this->command->info('✓ ' . count($revistas) . ' revistas científicas insertadas en t_revistacientifica.');
    }
}
