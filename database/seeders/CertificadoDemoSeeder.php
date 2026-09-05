<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CertificadoDemoSeeder extends Seeder
{
    private const MATERIA_ID = 9001;

    private const IMPARTE_ID = 9001;

    private const USUARIO_BASE = 9001;

    public function run(): void
    {
        DB::table('t_materia')->updateOrInsert(
            ['id_mat' => self::MATERIA_ID, 'id_us_reg' => 0],
            [
                'id_mat' => self::MATERIA_ID,
                'id_us_reg' => 0,
                'num_mat' => 1,
                'sigla' => 'DEMO-2026',
                'nombremat' => 'Diplomado en Gestión Pública',
                'nombre' => 'Diplomado en Gestión Pública',
                'carga_horaria' => '240',
                'estado' => 1,
                'fecha_reg' => now(),
            ]
        );

        DB::table('t_imparte')->updateOrInsert(
            ['id_imp' => self::IMPARTE_ID, 'id_us_reg' => 0],
            [
                'id_imp' => self::IMPARTE_ID,
                'id_us_reg' => 0,
                'num_imp' => 1,
                'periodo' => '2026-I',
                'gestion' => '2026',
                'id_mat' => self::MATERIA_ID,
                'paralelo' => 'A',
                'titulo_personalizado' => 'DIPLOMADO EN GESTIÓN PÚBLICA',
                'subtitulo_personalizado' => 'MODALIDAD VIRTUAL',
                'imparte_fecha_inicio' => '2026-01-15',
                'imparte_fecha_fin' => '2026-04-15',
                'estado' => 1,
                'fecha_reg' => now(),
            ]
        );

        $estudiantes = [
            ['nombre' => 'María Alejandra', 'appaterno' => 'Gutierrez',  'apmaterno' => 'Flores',    'ci' => '4521301', 'email' => 'maria.gutierrez@demo.bo'],
            ['nombre' => 'Carlos Eduardo',  'appaterno' => 'Mamani',     'apmaterno' => 'Quispe',    'ci' => '3812456', 'email' => 'carlos.mamani@demo.bo'],
            ['nombre' => 'Ana Lucia',        'appaterno' => 'Vargas',     'apmaterno' => 'Mendoza',   'ci' => '5103782', 'email' => 'ana.vargas@demo.bo'],
            ['nombre' => 'Diego Sebastián', 'appaterno' => 'Torrez',     'apmaterno' => 'Salinas',   'ci' => '6234519', 'email' => 'diego.torrez@demo.bo'],
            ['nombre' => 'Patricia',         'appaterno' => 'Condori',    'apmaterno' => 'Apaza',     'ci' => '7345821', 'email' => 'patricia.condori@demo.bo'],
            ['nombre' => 'Roberto Alfredo',  'appaterno' => 'Huanca',     'apmaterno' => 'Choque',    'ci' => '2456193', 'email' => 'roberto.huanca@demo.bo'],
            ['nombre' => 'Sandra Paola',     'appaterno' => 'Palacios',   'apmaterno' => 'Rojas',     'ci' => '8567234', 'email' => 'sandra.palacios@demo.bo'],
            ['nombre' => 'Fernando José',    'appaterno' => 'Balcazar',   'apmaterno' => 'Quiroga',   'ci' => '9678345', 'email' => 'fernando.balcazar@demo.bo'],
            ['nombre' => 'Claudia Beatriz',  'appaterno' => 'Espinoza',   'apmaterno' => 'Castro',    'ci' => '1234567', 'email' => 'claudia.espinoza@demo.bo'],
            ['nombre' => 'Javier Ernesto',   'appaterno' => 'Villanueva', 'apmaterno' => 'Morales',   'ci' => '3456789', 'email' => 'javier.villanueva@demo.bo'],
        ];

        foreach ($estudiantes as $i => $est) {
            $idUs = self::USUARIO_BASE + $i;
            DB::table('t_usuario')->updateOrInsert(
                ['id_us' => $idUs, 'id_us_reg' => 0],
                [
                    'id_us' => $idUs,
                    'id_us_reg' => 0,
                    'nombre' => $est['nombre'],
                    'appaterno' => $est['appaterno'],
                    'apmaterno' => $est['apmaterno'],
                    'ci' => $est['ci'],
                    'email' => $est['email'],
                    'celular' => '7'.str_pad($i + 1, 7, '0', STR_PAD_LEFT),
                    'estado' => 1,
                    'id_niv' => 3,
                    'fecha_reg' => now(),
                ]
            );
        }

        $notas = [85.0, 78.5, 91.0, 67.0, 88.5, 73.0, 95.5, 62.5, 80.0, 89.0];

        foreach ($estudiantes as $i => $est) {
            $idUs = self::USUARIO_BASE + $i;

            DB::table('t_lista_aprobados')->updateOrInsert(
                ['imparte_id' => self::IMPARTE_ID, 'usuario_id' => $idUs],
                [
                    'imparte_id' => self::IMPARTE_ID,
                    'usuario_id' => $idUs,
                    'nota_final' => $notas[$i],
                    'nota_minima' => 51.0,
                    'condicion' => $notas[$i] >= 51 ? 'aprobado' : 'reprobado',
                    'estado_certificado' => 'pendiente',
                    'registrado_por' => null,
                    'id_us_reg' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('✓ CertificadoDemoSeeder ejecutado:');
        $this->command->info('  → imparte_id: '.self::IMPARTE_ID.'  (usar este ID para generar certificados)');
        $this->command->info('  → '.count($estudiantes).' estudiantes aprobados con estado_certificado = pendiente');
    }
}
