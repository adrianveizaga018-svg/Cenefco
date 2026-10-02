<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CertPlantillaSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $plantillas = [
            [
                'nombre'         => 'Plantilla Aprobación — Horizontal',
                'tipo'           => 'aprobacion',
                'imagen_url'     => '',
                'ancho_px'       => 3508,
                'alto_px'        => 2480,
                'orientacion'    => 'horizontal',
                'formato_salida' => 'jpg',
                'calidad_jpg'    => 95,
                'fuente_default' => 'Arial',
                'color_default'  => '#000000',
                'estado'         => 'activo',
                'notas'          => 'Plantilla base para certificados de aprobación con nota.',
                'id_us_reg'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'nombre'         => 'Plantilla Participación — Horizontal',
                'tipo'           => 'participacion',
                'imagen_url'     => '',
                'ancho_px'       => 3508,
                'alto_px'        => 2480,
                'orientacion'    => 'horizontal',
                'formato_salida' => 'jpg',
                'calidad_jpg'    => 95,
                'fuente_default' => 'Arial',
                'color_default'  => '#000000',
                'estado'         => 'activo',
                'notas'          => 'Plantilla base para certificados de participación (sin nota).',
                'id_us_reg'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
        ];

        foreach ($plantillas as $plantilla) {
            DB::table('t_cert_plantilla')->updateOrInsert(
                ['nombre' => $plantilla['nombre']],
                $plantilla
            );
        }

        
        $this->insertarCamposBase();

        $total = DB::table('t_cert_plantilla')->count();
        $this->command->info("✓ {$total} plantillas en t_cert_plantilla.");
    }

    private function insertarCamposBase(): void
    {
        $plantillas = DB::table('t_cert_plantilla')->get();

        foreach ($plantillas as $plantilla) {
            $camposExistentes = DB::table('t_cert_plantilla_campo')
                ->where('plantilla_id', $plantilla->id)
                ->count();

            if ($camposExistentes > 0) {
                continue;
            }

            $esAprobacion = $plantilla->tipo === 'aprobacion';

            $campos = [
                ['clave' => 'nombre_participante', 'etiqueta' => 'Nombre del participante', 'tipo' => 'text',   'pos_x_pct' => 15.00, 'pos_y_pct' => 38.00, 'ancho_pct' => 70.00, 'alto_pct' => 8.00,  'tamano_pt' => 28, 'negrita' => true,  'mayusculas' => 'upper',  'orden' => 1],
                ['clave' => 'texto_certifica',     'etiqueta' => 'Texto "certifica que"',   'tipo' => 'text',   'pos_x_pct' => 20.00, 'pos_y_pct' => 30.00, 'ancho_pct' => 60.00, 'alto_pct' => 5.00,  'tamano_pt' => 14, 'negrita' => false, 'mayusculas' => 'none',   'orden' => 2],
                ['clave' => 'nombre_programa',     'etiqueta' => 'Nombre del programa',     'tipo' => 'text',   'pos_x_pct' => 10.00, 'pos_y_pct' => 50.00, 'ancho_pct' => 80.00, 'alto_pct' => 7.00,  'tamano_pt' => 18, 'negrita' => true,  'mayusculas' => 'title',  'orden' => 3],
                ['clave' => 'horas_academicas',    'etiqueta' => 'Horas académicas',        'tipo' => 'text',   'pos_x_pct' => 30.00, 'pos_y_pct' => 60.00, 'ancho_pct' => 40.00, 'alto_pct' => 5.00,  'tamano_pt' => 13, 'negrita' => false, 'mayusculas' => 'none',   'orden' => 4],
                ['clave' => 'fecha_emision',       'etiqueta' => 'Fecha de emisión',        'tipo' => 'date',   'pos_x_pct' => 30.00, 'pos_y_pct' => 72.00, 'ancho_pct' => 40.00, 'alto_pct' => 5.00,  'tamano_pt' => 13, 'negrita' => false, 'mayusculas' => 'none',   'orden' => 5],
                ['clave' => 'codigo_verificacion', 'etiqueta' => 'Código de verificación',  'tipo' => 'text',   'pos_x_pct' => 70.00, 'pos_y_pct' => 85.00, 'ancho_pct' => 25.00, 'alto_pct' => 4.00,  'tamano_pt' => 9,  'negrita' => false, 'mayusculas' => 'none',   'orden' => 6],
                ['clave' => 'qr_verificacion',     'etiqueta' => 'QR de verificación',      'tipo' => 'qr',     'pos_x_pct' => 83.00, 'pos_y_pct' => 70.00, 'ancho_pct' => 12.00, 'alto_pct' => 18.00, 'tamano_pt' => 12, 'negrita' => false, 'mayusculas' => 'none',   'orden' => 7],
            ];

            if ($esAprobacion) {
                $campos[] = ['clave' => 'nota_final', 'etiqueta' => 'Nota final', 'tipo' => 'number', 'pos_x_pct' => 38.00, 'pos_y_pct' => 57.00, 'ancho_pct' => 24.00, 'alto_pct' => 5.00, 'tamano_pt' => 16, 'negrita' => true, 'mayusculas' => 'none', 'orden' => 8];
            }

            foreach ($campos as $campo) {
                DB::table('t_cert_plantilla_campo')->insert(array_merge($campo, [
                    'plantilla_id' => $plantilla->id,
                    'color'        => '#1a1a1a',
                    'alineacion'   => 'center',
                    'cursiva'      => false,
                    'valor_fijo'   => null,
                    'activo'       => true,
                ]));
            }
        }

        $totalCampos = DB::table('t_cert_plantilla_campo')->count();
        $this->command->info("✓ {$totalCampos} campos en t_cert_plantilla_campo.");
    }
}
