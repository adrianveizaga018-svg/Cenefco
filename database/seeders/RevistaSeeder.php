<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RevistaSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        $revistas = [
            [
                'id_revista'          => 1,
                'num_revista'         => 1,
                'titulo_revista'      => 'CENEFCO Informa — Boletín de Formación Continua Vol. 1',
                'descripcion_revista' => 'Primera edición del boletín institucional de CENEFCO. Incluye artículos sobre tendencias en formación continua, novedades académicas, entrevistas a docentes destacados y una agenda de próximos programas para el primer semestre.',
                'fecha_publicacion'   => '2024-01-15',
                'archivo'             => null,
            ],
            [
                'id_revista'          => 2,
                'num_revista'         => 2,
                'titulo_revista'      => 'CENEFCO Informa — Boletín de Formación Continua Vol. 2',
                'descripcion_revista' => 'Segunda edición con cobertura especial de los programas de diplomado concluidos en el primer semestre, testimonios de egresados, estadísticas de empleabilidad y presentación de nuevos convenios interinstitucionales.',
                'fecha_publicacion'   => '2024-07-10',
                'archivo'             => null,
            ],
            [
                'id_revista'          => 3,
                'num_revista'         => 3,
                'titulo_revista'      => 'CENEFCO Informa — Boletín de Formación Continua Vol. 3',
                'descripcion_revista' => 'Edición especial dedicada a la educación virtual y las tecnologías emergentes en el ámbito educativo. Artículos sobre inteligencia artificial en el aula, gamificación, y el uso de plataformas LMS en Bolivia.',
                'fecha_publicacion'   => '2024-11-20',
                'archivo'             => null,
            ],
            [
                'id_revista'          => 4,
                'num_revista'         => 4,
                'titulo_revista'      => 'CENEFCO Informa — Boletín de Formación Continua Vol. 4',
                'descripcion_revista' => 'Edición de inicio de gestión 2025. Presenta la oferta académica completa del año, el nuevo modelo pedagógico institucional, perfiles de los nuevos docentes incorporados y una retrospectiva de logros 2024.',
                'fecha_publicacion'   => '2025-02-03',
                'archivo'             => null,
            ],
            [
                'id_revista'          => 5,
                'num_revista'         => 5,
                'titulo_revista'      => 'CENEFCO Informa — Boletín de Formación Continua Vol. 5',
                'descripcion_revista' => 'Número especial sobre internacionalización y cooperación académica. Cubre los acuerdos firmados con universidades extranjeras, programas de movilidad docente y las oportunidades de formación internacional para estudiantes del CENEFCO.',
                'fecha_publicacion'   => '2025-05-15',
                'archivo'             => null,
            ],
        ];

        foreach ($revistas as $r) {
            if (DB::table('t_revista')->where('id_revista', $r['id_revista'])->where('id_us_reg', 1)->exists()) {
                if (! DB::table('t_revista')->where('id_revista', $r['id_revista'])->whereNotNull('slug')->exists()) {
                    DB::table('t_revista')->where('id_revista', $r['id_revista'])->update(['slug' => Str::slug($r['titulo_revista'])]);
                }
                continue;
            }
            DB::table('t_revista')->insertOrIgnore(array_merge($r, [
                'slug'          => Str::slug($r['titulo_revista']),
                'id_us_reg'     => 1,
                'estado'        => 1,
                'fecha_reg'     => $now,
                'per_modificar' => 0,
            ]));
        }

        $this->command->info('✓ ' . count($revistas) . ' revistas insertadas en t_revista.');
    }
}
