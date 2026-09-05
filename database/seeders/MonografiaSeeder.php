<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MonografiaSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        $monografias = [
            [
                'id_monografia'          => 1,
                'num_monografia'         => 1,
                'titulo_monografia'      => 'La formación continua como estrategia de desarrollo profesional en el sector público boliviano',
                'descripcion_monografia' => 'Monografía que examina el rol de los programas de capacitación continua en el fortalecimiento de las capacidades del personal del sector público, con énfasis en las instituciones gubernamentales del eje troncal boliviano.',
                'fecha_publicacion'      => '2024-03-20',
                'autor'                  => 'Jorge Antonio Mamani Larico',
                'archivo'                => null,
            ],
            [
                'id_monografia'          => 2,
                'num_monografia'         => 2,
                'titulo_monografia'      => 'Liderazgo transformacional en organizaciones educativas: características y aplicaciones prácticas',
                'descripcion_monografia' => 'Análisis del modelo de liderazgo transformacional y su aplicabilidad en la gestión de centros de formación, describiendo casos prácticos de instituciones educativas bolivianas que han adoptado este enfoque.',
                'fecha_publicacion'      => '2024-08-14',
                'autor'                  => 'Patricia Eugenia Salinas Vda. de Morales',
                'archivo'                => null,
            ],
            [
                'id_monografia'          => 3,
                'num_monografia'         => 3,
                'titulo_monografia'      => 'Gestión financiera para pequeñas y medianas empresas: herramientas básicas de análisis',
                'descripcion_monografia' => 'Monografía orientada a propietarios y administradores de PYMES bolivianas, presentando herramientas prácticas de análisis financiero, control de costos y planificación presupuestaria adaptadas al contexto nacional.',
                'fecha_publicacion'      => '2024-10-05',
                'autor'                  => 'Ximena Carolina Balcázar Mendoza',
                'archivo'                => null,
            ],
            [
                'id_monografia'          => 4,
                'num_monografia'         => 4,
                'titulo_monografia'      => 'Tecnologías de la información aplicadas a la educación superior: herramientas y tendencias actuales',
                'descripcion_monografia' => 'Revisión bibliográfica y análisis de las principales herramientas tecnológicas utilizadas en la educación superior contemporánea, incluyendo inteligencia artificial generativa, realidad aumentada y plataformas colaborativas en la nube.',
                'fecha_publicacion'      => '2025-01-22',
                'autor'                  => 'Marco Antonio Tito Flores',
                'archivo'                => null,
            ],
            [
                'id_monografia'          => 5,
                'num_monografia'         => 5,
                'titulo_monografia'      => 'Comunicación organizacional efectiva: estrategias para entornos de trabajo híbrido',
                'descripcion_monografia' => 'Estudio de las barreras y facilitadores de la comunicación en organizaciones con equipos distribuidos entre trabajo presencial y remoto, con recomendaciones para la mejora de la coordinación y el clima organizacional.',
                'fecha_publicacion'      => '2025-03-17',
                'autor'                  => 'Valeria Susana Huanca Callisaya',
                'archivo'                => null,
            ],
            [
                'id_monografia'          => 6,
                'num_monografia'         => 6,
                'titulo_monografia'      => 'Evaluación de la calidad en programas de formación técnica: modelos e indicadores',
                'descripcion_monografia' => 'Revisión de los principales modelos de evaluación de la calidad educativa (ISO 21001, EFQM, CINDA) y su aplicabilidad en programas de formación técnica y profesional en Bolivia.',
                'fecha_publicacion'      => '2025-06-03',
                'autor'                  => 'Hernán Rodrigo Apaza Condori',
                'archivo'                => null,
            ],
            [
                'id_monografia'          => 7,
                'num_monografia'         => 7,
                'titulo_monografia'      => 'Marketing digital para instituciones educativas: captación y fidelización de estudiantes',
                'descripcion_monografia' => 'Monografía que analiza las estrategias de marketing digital más efectivas para instituciones de formación continua, incluyendo gestión de redes sociales, SEO, email marketing y publicidad programática en el contexto boliviano.',
                'fecha_publicacion'      => '2024-12-11',
                'autor'                  => 'Sofía Isabel Ramos Chura',
                'archivo'                => null,
            ],
        ];

        foreach ($monografias as $m) {
            if (DB::table('t_monografia')->where('id_monografia', $m['id_monografia'])->where('id_us_reg', 1)->exists()) {
                if (! DB::table('t_monografia')->where('id_monografia', $m['id_monografia'])->whereNotNull('slug')->exists()) {
                    DB::table('t_monografia')->where('id_monografia', $m['id_monografia'])->update(['slug' => Str::slug($m['titulo_monografia'])]);
                }
                continue;
            }
            DB::table('t_monografia')->insertOrIgnore(array_merge($m, [
                'slug'          => Str::slug($m['titulo_monografia']),
                'id_us_reg'     => 1,
                'estado'        => 1,
                'fecha_reg'     => $now,
                'per_modificar' => 0,
            ]));
        }

        $this->command->info('✓ ' . count($monografias) . ' monografías insertadas en t_monografia.');
    }
}
