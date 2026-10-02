<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebEventoSeeder extends Seeder
{
    public function run(): void
    {
        $eventos = [
            [
                'titulo'      => 'Conferencia Internacional de Formación Continua 2025',
                'entradilla'  => 'Un espacio de encuentro para expertos en educación continua y formación profesional.',
                'descripcion' => 'El CENEFCO organiza su conferencia anual donde especialistas nacionales e internacionales debatirán sobre las tendencias en formación continua, competencias digitales y aprendizaje a lo largo de la vida.',
                'tipo'        => 'Conferencia',
                'modalidad'   => 'presencial',
                'lugar'       => 'Auditorio Central CENEFCO, La Paz',
                'fecha_inicio'=> '2025-08-15 09:00:00',
                'fecha_fin'   => '2025-08-15 18:00:00',
                'todo_el_dia' => false,
                'estado'      => 'programado',
                'destacado'   => true,
                'gratuito'    => true,
                'publico'     => true,
            ],
            [
                'titulo'      => 'Taller de Metodologías Activas en el Aula Virtual',
                'entradilla'  => 'Desarrolla competencias para diseñar experiencias de aprendizaje efectivas en entornos virtuales.',
                'descripcion' => 'Taller práctico dirigido a docentes y formadores que deseen incorporar metodologías activas como el aprendizaje basado en proyectos, flipped classroom y gamificación en sus clases virtuales.',
                'tipo'        => 'Taller',
                'modalidad'   => 'virtual',
                'lugar'       => 'Plataforma Zoom CENEFCO',
                'url_transmision' => 'https://zoom.us/j/cenefco-taller-2025',
                'fecha_inicio'=> '2025-09-05 14:00:00',
                'fecha_fin'   => '2025-09-05 18:00:00',
                'todo_el_dia' => false,
                'estado'      => 'programado',
                'destacado'   => false,
                'gratuito'    => false,
                'precio'      => 80.00,
                'publico'     => true,
            ],
            [
                'titulo'      => 'Seminario de Gestión del Talento Humano',
                'entradilla'  => 'Estrategias modernas para la gestión efectiva del capital humano en organizaciones bolivianas.',
                'descripcion' => 'Seminario intensivo de dos días que aborda las tendencias en selección de personal, evaluación del desempeño, clima organizacional y planes de carrera adaptados al contexto empresarial boliviano.',
                'tipo'        => 'Seminario',
                'modalidad'   => 'híbrido',
                'lugar'       => 'Sala de Conferencias CENEFCO, Cochabamba',
                'fecha_inicio'=> '2025-10-10 08:00:00',
                'fecha_fin'   => '2025-10-11 17:00:00',
                'todo_el_dia' => false,
                'estado'      => 'programado',
                'destacado'   => true,
                'gratuito'    => false,
                'precio'      => 150.00,
                'cupo_maximo' => 40,
                'publico'     => true,
            ],
            [
                'titulo'      => 'Webinar: Inteligencia Artificial Aplicada a la Educación',
                'entradilla'  => 'Descubre cómo la IA está transformando los procesos de enseñanza y aprendizaje.',
                'descripcion' => 'Webinar gratuito donde exploraremos herramientas de inteligencia artificial que están revolucionando la educación: ChatGPT en el aula, generación automática de materiales didácticos y evaluación asistida por IA.',
                'tipo'        => 'Webinar',
                'modalidad'   => 'virtual',
                'lugar'       => null,
                'url_transmision' => 'https://meet.google.com/cenefco-ia-educacion',
                'fecha_inicio'=> '2025-07-25 19:00:00',
                'fecha_fin'   => '2025-07-25 21:00:00',
                'todo_el_dia' => false,
                'estado'      => 'realizado',
                'destacado'   => false,
                'gratuito'    => true,
                'publico'     => true,
            ],
            [
                'titulo'      => 'Ceremonia de Graduación Diplomado en Gestión Empresarial',
                'entradilla'  => 'Celebramos el logro de nuestros graduados del Diplomado en Gestión Empresarial Edición 2024.',
                'descripcion' => 'Acto solemne de graduación para los 35 participantes que culminaron exitosamente el Diplomado en Gestión Empresarial. Contaremos con la presencia de autoridades académicas y empresariales.',
                'tipo'        => 'Graduación',
                'modalidad'   => 'presencial',
                'lugar'       => 'Salón de Actos CENEFCO, La Paz',
                'fecha_inicio'=> '2025-06-28 10:00:00',
                'fecha_fin'   => '2025-06-28 13:00:00',
                'todo_el_dia' => false,
                'estado'      => 'realizado',
                'destacado'   => true,
                'gratuito'    => true,
                'publico'     => true,
            ],
        ];

        foreach ($eventos as $evento) {
            $slug = Str::slug($evento['titulo']);
            $baseSlug = $slug;
            $count = 1;
            while (DB::table('web_evento')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count++;
            }

            $now = now();
            DB::table('web_evento')->updateOrInsert(
                ['titulo' => $evento['titulo']],
                [
                    'titulo'          => $evento['titulo'],
                    'slug'            => $slug,
                    'entradilla'      => $evento['entradilla'] ?? null,
                    'descripcion'     => $evento['descripcion'] ?? null,
                    'imagen_url'      => null,
                    'tipo'            => $evento['tipo'] ?? null,
                    'modalidad'       => $evento['modalidad'] ?? 'presencial',
                    'lugar'           => $evento['lugar'] ?? null,
                    'url_transmision' => $evento['url_transmision'] ?? null,
                    'gratuito'        => $evento['gratuito'] ?? true,
                    'precio'          => $evento['precio'] ?? null,
                    'cupo_maximo'     => $evento['cupo_maximo'] ?? null,
                    'fecha_inicio'    => $evento['fecha_inicio'],
                    'fecha_fin'       => $evento['fecha_fin'] ?? null,
                    'todo_el_dia'     => $evento['todo_el_dia'] ?? false,
                    'destacado'       => $evento['destacado'] ?? false,
                    'estado'          => $evento['estado'] ?? 'publicado',
                    'vistas'          => 0,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]
            );
        }
    }
}
