<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalendarioAcademicoSeeder extends Seeder
{
    public function run(): void
    {
        
        $colores = [
            'inscripciones' => '#3b82f6',
            'inicio_clases' => '#10b981',
            'finalizacion'  => '#f59e0b',
            'evaluacion'    => '#8b5cf6',
            'graduacion'    => '#ec4899',
            'feriado'       => '#ef4444',
            'otro'          => '#6b7280',
        ];

        $eventos = [

            
            [
                'titulo'       => 'Inicio del Período Académico 2026-I',
                'descripcion'  => 'Arranque oficial de todos los programas del primer semestre 2026.',
                'tipo'         => 'inicio_clases',
                'programa_id'  => null,
                'fecha_inicio' => '2026-05-06',
                'fecha_fin'    => null,
                'todo_el_dia'  => true,
                'destacado'    => true,
                'publico'      => true,
            ],
            [
                'titulo'       => 'Feriado Nacional — Día del Trabajo',
                'descripcion'  => 'Feriado nacional. No hay actividades académicas.',
                'tipo'         => 'feriado',
                'programa_id'  => null,
                'fecha_inicio' => '2026-05-01',
                'fecha_fin'    => null,
                'todo_el_dia'  => true,
                'destacado'    => false,
                'publico'      => true,
            ],
            [
                'titulo'       => 'Feriado Nacional — Corpus Christi',
                'descripcion'  => 'Feriado nacional. Suspensión de clases.',
                'tipo'         => 'feriado',
                'programa_id'  => null,
                'fecha_inicio' => '2026-06-04',
                'fecha_fin'    => null,
                'todo_el_dia'  => true,
                'destacado'    => false,
                'publico'      => true,
            ],
            [
                'titulo'       => 'Cierre de inscripciones — Todos los programas 2026-I',
                'descripcion'  => 'Fecha límite para completar el proceso de inscripción en todos los programas del semestre.',
                'tipo'         => 'inscripciones',
                'programa_id'  => null,
                'fecha_inicio' => '2026-04-28',
                'fecha_fin'    => null,
                'todo_el_dia'  => true,
                'destacado'    => true,
                'publico'      => true,
            ],
            [
                'titulo'       => 'Ceremonia de Graduación — Semestre 2025-II',
                'descripcion'  => 'Acto de graduación de todos los estudiantes que finalizaron sus programas en el semestre 2025-II.',
                'tipo'         => 'graduacion',
                'programa_id'  => null,
                'fecha_inicio' => '2026-05-30',
                'fecha_fin'    => null,
                'todo_el_dia'  => true,
                'destacado'    => true,
                'publico'      => true,
            ],

            
            [
                'titulo'       => 'Inscripciones — Diplomado en Gestión Pública',
                'descripcion'  => 'Período de inscripciones para el Diplomado en Gestión Pública y Administración del Estado.',
                'tipo'         => 'inscripciones',
                'programa_id'  => 1,
                'fecha_inicio' => '2026-04-15',
                'fecha_fin'    => '2026-05-09',
                'todo_el_dia'  => true,
                'destacado'    => false,
                'publico'      => true,
            ],
            [
                'titulo'       => 'Inicio de Clases — Diplomado en Gestión Pública',
                'descripcion'  => 'Primera sesión del Diplomado en Gestión Pública y Administración del Estado.',
                'tipo'         => 'inicio_clases',
                'programa_id'  => 1,
                'fecha_inicio' => '2026-05-10',
                'fecha_fin'    => null,
                'todo_el_dia'  => true,
                'destacado'    => true,
                'publico'      => true,
            ],
            [
                'titulo'       => 'Evaluación Parcial — Diplomado en Gestión Pública',
                'descripcion'  => 'Evaluación de medio término del Diplomado en Gestión Pública.',
                'tipo'         => 'evaluacion',
                'programa_id'  => 1,
                'fecha_inicio' => '2026-07-18',
                'fecha_fin'    => null,
                'todo_el_dia'  => true,
                'destacado'    => false,
                'publico'      => false,
            ],
            [
                'titulo'       => 'Finalización — Diplomado en Gestión Pública',
                'descripcion'  => 'Última sesión y entrega de trabajos finales del Diplomado en Gestión Pública.',
                'tipo'         => 'finalizacion',
                'programa_id'  => 1,
                'fecha_inicio' => '2026-09-27',
                'fecha_fin'    => null,
                'todo_el_dia'  => true,
                'destacado'    => false,
                'publico'      => true,
            ],

            
            [
                'titulo'       => 'Inscripciones — Diplomado en Derecho Empresarial',
                'descripcion'  => 'Período de inscripciones para el Diplomado en Derecho Empresarial y Contratos Comerciales.',
                'tipo'         => 'inscripciones',
                'programa_id'  => 2,
                'fecha_inicio' => '2026-04-28',
                'fecha_fin'    => '2026-05-22',
                'todo_el_dia'  => true,
                'destacado'    => false,
                'publico'      => true,
            ],
            [
                'titulo'       => 'Inicio de Clases — Diplomado en Derecho Empresarial',
                'descripcion'  => 'Primera sesión del Diplomado en Derecho Empresarial y Contratos Comerciales.',
                'tipo'         => 'inicio_clases',
                'programa_id'  => 2,
                'fecha_inicio' => '2026-05-23',
                'fecha_fin'    => null,
                'todo_el_dia'  => true,
                'destacado'    => true,
                'publico'      => true,
            ],
            [
                'titulo'       => 'Finalización — Diplomado en Derecho Empresarial',
                'descripcion'  => 'Cierre del Diplomado en Derecho Empresarial y Contratos Comerciales.',
                'tipo'         => 'finalizacion',
                'programa_id'  => 2,
                'fecha_inicio' => '2026-10-17',
                'fecha_fin'    => null,
                'todo_el_dia'  => true,
                'destacado'    => false,
                'publico'      => true,
            ],

            
            [
                'titulo'       => 'Inscripciones — Excel Avanzado para Negocios',
                'descripcion'  => 'Período de inscripciones para el Curso de Excel Avanzado para Negocios y Finanzas.',
                'tipo'         => 'inscripciones',
                'programa_id'  => 3,
                'fecha_inicio' => '2026-04-20',
                'fecha_fin'    => '2026-05-05',
                'todo_el_dia'  => true,
                'destacado'    => false,
                'publico'      => true,
            ],
            [
                'titulo'       => 'Inicio y Finalización — Curso Excel Avanzado',
                'descripcion'  => 'Curso intensivo de Excel Avanzado para Negocios y Finanzas.',
                'tipo'         => 'inicio_clases',
                'programa_id'  => 3,
                'fecha_inicio' => '2026-05-06',
                'fecha_fin'    => '2026-05-28',
                'todo_el_dia'  => true,
                'destacado'    => false,
                'publico'      => true,
            ],

            
            [
                'titulo'       => 'Inscripciones — MBA (Maestría en Administración)',
                'descripcion'  => 'Período de inscripciones y proceso de admisión para la Maestría en Administración de Empresas.',
                'tipo'         => 'inscripciones',
                'programa_id'  => 6,
                'fecha_inicio' => '2026-04-28',
                'fecha_fin'    => '2026-06-05',
                'todo_el_dia'  => true,
                'destacado'    => true,
                'publico'      => true,
            ],
            [
                'titulo'       => 'Inicio de Clases — MBA',
                'descripcion'  => 'Inicio oficial de la Maestría en Administración de Empresas.',
                'tipo'         => 'inicio_clases',
                'programa_id'  => 6,
                'fecha_inicio' => '2026-06-06',
                'fecha_fin'    => null,
                'todo_el_dia'  => true,
                'destacado'    => true,
                'publico'      => true,
            ],

            
            [
                'titulo'       => 'Seminario de Actualización Tributaria 2026',
                'descripcion'  => 'Evento de un solo día: NIT, Facturas y Declaraciones. Cupos muy limitados.',
                'tipo'         => 'otro',
                'programa_id'  => 8,
                'fecha_inicio' => '2026-05-22',
                'fecha_fin'    => null,
                'todo_el_dia'  => true,
                'destacado'    => true,
                'publico'      => true,
            ],

            
            [
                'titulo'       => 'Evaluación Final — Certificación en Gestión de Proyectos',
                'descripcion'  => 'Evaluación y presentación de proyectos finales para la Certificación en Gestión de Proyectos (Fundamentos PMI).',
                'tipo'         => 'evaluacion',
                'programa_id'  => 9,
                'fecha_inicio' => '2026-06-27',
                'fecha_fin'    => null,
                'todo_el_dia'  => true,
                'destacado'    => false,
                'publico'      => false,
            ],
        ];

        foreach ($eventos as $evento) {
            $yaExiste = DB::table('web_calendario_academico')
                ->where('titulo', $evento['titulo'])
                ->where('fecha_inicio', $evento['fecha_inicio'])
                ->exists();

            if ($yaExiste) {
                continue;
            }

            DB::table('web_calendario_academico')->insertOrIgnore([
                'titulo'       => $evento['titulo'],
                'descripcion'  => $evento['descripcion'],
                'tipo'         => $evento['tipo'],
                'color'        => $colores[$evento['tipo']] ?? '#6b7280',
                'programa_id'  => $evento['programa_id'],
                'fecha_inicio' => $evento['fecha_inicio'],
                'fecha_fin'    => $evento['fecha_fin'],
                'todo_el_dia'  => $evento['todo_el_dia'] ? 1 : 0,
                'destacado'    => $evento['destacado'] ? 1 : 0,
                'publico'      => $evento['publico'] ? 1 : 0,
                'created_at'   => now()->toDateTimeString(),
                'updated_at'   => now()->toDateTimeString(),
            ]);
        }
    }
}
