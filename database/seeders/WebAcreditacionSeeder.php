<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebAcreditacionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        $registros = [
            [
                'nombre'           => 'Acreditación Nacional de Excelencia Académica',
                'entidad_otorgante'=> 'Ministerio de Educación de Bolivia',
                'tipo'             => 'nacional',
                'descripcion'      => 'Reconocimiento oficial del Ministerio de Educación por el cumplimiento de estándares de calidad en la formación continua y programas de postgrado.',
                'logo_url'         => null,
                'logo_alt'         => 'Ministerio de Educación Bolivia',
                'url_verificacion' => null,
                'fecha_obtencion'  => '2022-03-15',
                'fecha_vencimiento'=> '2027-03-15',
                'orden'            => 1,
                'activo'           => 1,
            ],
            [
                'nombre'           => 'Certificación ISO 9001:2015 — Gestión de Calidad',
                'entidad_otorgante'=> 'Bureau Veritas Bolivia',
                'tipo'             => 'internacional',
                'descripcion'      => 'Certificación internacional que garantiza la gestión de calidad en los procesos académicos y administrativos de CENEFCO.',
                'logo_url'         => null,
                'logo_alt'         => 'ISO 9001 Bureau Veritas',
                'url_verificacion' => null,
                'fecha_obtencion'  => '2023-06-01',
                'fecha_vencimiento'=> '2026-06-01',
                'orden'            => 2,
                'activo'           => 1,
            ],
            [
                'nombre'           => 'Aval Académico — Universidad Mayor de San Simón',
                'entidad_otorgante'=> 'Universidad Mayor de San Simón (UMSS)',
                'tipo'             => 'aval_universitario',
                'descripcion'      => 'Aval académico otorgado por la UMSS que respalda la calidad y el nivel académico de los programas de diplomado y especialización.',
                'logo_url'         => null,
                'logo_alt'         => 'UMSS Cochabamba',
                'url_verificacion' => null,
                'fecha_obtencion'  => '2021-09-10',
                'fecha_vencimiento'=> null,
                'orden'            => 3,
                'activo'           => 1,
            ],
            [
                'nombre'           => 'Miembro de la Red Iberoamericana de Postgrado',
                'entidad_otorgante'=> 'Red Iberoamericana de Postgrado (RIEP)',
                'tipo'             => 'membresia',
                'descripcion'      => 'Membresía activa en la red que conecta instituciones de educación de postgrado en Iberoamérica, facilitando el intercambio académico y la movilidad docente.',
                'logo_url'         => null,
                'logo_alt'         => 'Red Iberoamericana de Postgrado',
                'url_verificacion' => null,
                'fecha_obtencion'  => '2020-01-20',
                'fecha_vencimiento'=> null,
                'orden'            => 4,
                'activo'           => 1,
            ],
        ];

        $insertados = 0;
        foreach ($registros as $r) {
            $existe = DB::table('web_acreditacion')
                ->where('nombre', $r['nombre'])
                ->exists();

            if ($existe) continue;

            DB::table('web_acreditacion')->insertOrIgnore(array_merge($r, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
            $insertados++;
        }

        $this->command->info("✓ Acreditaciones: {$insertados} registros insertados en web_acreditacion.");
    }
}
