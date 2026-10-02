<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebDescargableSeeder extends Seeder
{
    public function run(): void
    {
        $descargables = [
            [
                'nombre'             => 'Brochure Institucional CENEFCO 2026',
                'tipo'               => 'brochure',
                'archivo_url'        => 'https://www.w3.org/WAI/WCAG21/Techniques/pdf/sample.pdf',
                'imagen_portada_url' => null,
                'programa_id'        => null,
                'requiere_datos'     => false,
                'descargas'          => 0,
                'orden'              => 0,
                'activo'             => true,
            ],
            [
                'nombre'             => 'Calendario Académico 2026',
                'tipo'               => 'calendario',
                'archivo_url'        => 'https://www.w3.org/WAI/WCAG21/Techniques/pdf/sample.pdf',
                'imagen_portada_url' => null,
                'programa_id'        => null,
                'requiere_datos'     => false,
                'descargas'          => 0,
                'orden'              => 1,
                'activo'             => true,
            ],
            [
                'nombre'             => 'Reglamento Estudiantil',
                'tipo'               => 'reglamento',
                'archivo_url'        => 'https://www.w3.org/WAI/WCAG21/Techniques/pdf/sample.pdf',
                'imagen_portada_url' => null,
                'programa_id'        => null,
                'requiere_datos'     => false,
                'descargas'          => 0,
                'orden'              => 2,
                'activo'             => true,
            ],
            [
                'nombre'             => 'Formulario de Preinscripción',
                'tipo'               => 'formulario',
                'archivo_url'        => 'https://www.w3.org/WAI/WCAG21/Techniques/pdf/sample.pdf',
                'imagen_portada_url' => null,
                'programa_id'        => null,
                'requiere_datos'     => true,
                'descargas'          => 0,
                'orden'              => 3,
                'activo'             => true,
            ],
            [
                'nombre'             => 'Guía de Becas y Financiamiento 2026',
                'tipo'               => 'guia',
                'archivo_url'        => 'https://www.w3.org/WAI/WCAG21/Techniques/pdf/sample.pdf',
                'imagen_portada_url' => null,
                'programa_id'        => null,
                'requiere_datos'     => true,
                'descargas'          => 0,
                'orden'              => 4,
                'activo'             => true,
            ],
            [
                'nombre'             => 'Plan de Estudios — Diplomado en Administración',
                'tipo'               => 'plan_estudios',
                'archivo_url'        => 'https://www.w3.org/WAI/WCAG21/Techniques/pdf/sample.pdf',
                'imagen_portada_url' => null,
                'programa_id'        => null,
                'requiere_datos'     => false,
                'descargas'          => 0,
                'orden'              => 5,
                'activo'             => true,
            ],
            [
                'nombre'             => 'Plan de Estudios — Diplomado en Derecho Tributario',
                'tipo'               => 'plan_estudios',
                'archivo_url'        => 'https://www.w3.org/WAI/WCAG21/Techniques/pdf/sample.pdf',
                'imagen_portada_url' => null,
                'programa_id'        => null,
                'requiere_datos'     => false,
                'descargas'          => 0,
                'orden'              => 6,
                'activo'             => true,
            ],
            [
                'nombre'             => 'Temario — Curso de Contabilidad Básica',
                'tipo'               => 'temario',
                'archivo_url'        => 'https://www.w3.org/WAI/WCAG21/Techniques/pdf/sample.pdf',
                'imagen_portada_url' => null,
                'programa_id'        => null,
                'requiere_datos'     => false,
                'descargas'          => 0,
                'orden'              => 7,
                'activo'             => true,
            ],
        ];

        foreach ($descargables as $item) {
            DB::table('web_descargable')->updateOrInsert(
                ['nombre' => $item['nombre']],
                $item
            );
        }

        $this->command->info('✓ Descargables creados: ' . count($descargables));
    }
}
