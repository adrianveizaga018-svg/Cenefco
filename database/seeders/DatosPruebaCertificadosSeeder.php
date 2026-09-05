<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatosPruebaCertificadosSeeder extends Seeder
{
    private const FECHAPACOS = [201, 202, 203];

    private const ID_IMP = 9001;

    private const ID_PLAN = 101;

    public function run(): void
    {
        $now = now()->toDateTimeString();

        $this->seedUsuariosEInscritos($now);
    }

    private function seedUsuariosEInscritos(string $now): void
    {
        $estudiantes = [
            
            ['id_us' => 9013, 'appaterno' => 'Alvarado',  'nombre' => 'Gabriela',   'ci' => 'TEST-9013', 'email' => 'gabriela.alvarado@test.bo',  'cel' => '72200001', 'pagos' => 'completo'],
            ['id_us' => 9014, 'appaterno' => 'Barrios',   'nombre' => 'Hernán',     'ci' => 'TEST-9014', 'email' => 'hernan.barrios@test.bo',      'cel' => '72200002', 'pagos' => 'completo'],
            ['id_us' => 9015, 'appaterno' => 'Camacho',   'nombre' => 'Inés',       'ci' => 'TEST-9015', 'email' => 'ines.camacho@test.bo',        'cel' => '72200003', 'pagos' => 'completo'],
            ['id_us' => 9016, 'appaterno' => 'Durán',     'nombre' => 'Javier',     'ci' => 'TEST-9016', 'email' => 'javier.duran@test.bo',        'cel' => '72200004', 'pagos' => 'completo'],
            
            ['id_us' => 9017, 'appaterno' => 'Estrada',   'nombre' => 'Karen',      'ci' => 'TEST-9017', 'email' => 'karen.estrada@test.bo',       'cel' => '72200005', 'pagos' => 'parcial'],
            ['id_us' => 9018, 'appaterno' => 'Fuentes',   'nombre' => 'Leonardo',   'ci' => 'TEST-9018', 'email' => 'leonardo.fuentes@test.bo',    'cel' => '72200006', 'pagos' => 'parcial'],
            
            ['id_us' => 9019, 'appaterno' => 'Gómez',     'nombre' => 'Marcela',    'ci' => 'TEST-9019', 'email' => 'marcela.gomez@test.bo',       'cel' => '72200007', 'pagos' => 'ninguno'],
            ['id_us' => 9020, 'appaterno' => 'Heredia',   'nombre' => 'Nicolás',    'ci' => 'TEST-9020', 'email' => 'nicolas.heredia@test.bo',     'cel' => '72200008', 'pagos' => 'ninguno'],
        ];

        $usInsertados   = 0;
        $insInsertados  = 0;
        $pagosInsertados = 0;

        foreach ($estudiantes as $est) {
            $existeUser = DB::table('t_usuario')
                ->where('id_us', $est['id_us'])
                ->where('id_us_reg', 0)
                ->exists();

            if (! $existeUser) {
                DB::table('t_usuario')->insertOrIgnore([
                    'id_us'          => $est['id_us'],
                    'id_us_reg'      => 0,
                    'num_us'         => 0,
                    'tipoestudiante' => 2,
                    'appaterno'      => $est['appaterno'],
                    'apmaterno'      => '',
                    'nombre'         => $est['nombre'],
                    'ci'             => $est['ci'],
                    'celular'        => $est['cel'],
                    'email'          => $est['email'],
                    'genero'         => 1,
                    'id_niv'         => 3,
                    'id_tipoprograma'=> 1,
                    'id_universidad' => 0,
                    'id_carrera'     => 0,
                    'id_prof'        => 0,
                    'fecha_reg'      => $now,
                    'estado'         => 1,
                    'per_modificar'  => 0,
                ]);
                $usInsertados++;
            }

            $existeIns = DB::table('t_inscripcion')
                ->where('id_us', $est['id_us'])
                ->where('id_imp', self::ID_IMP)
                ->exists();

            if (! $existeIns) {
                DB::table('t_inscripcion')->insertGetId([
                    'id_us_reg'       => 1,
                    'num_ins'         => 0,
                    'fecha_ins'       => '2026-01-20',
                    'id_us'           => $est['id_us'],
                    'id_imp'          => self::ID_IMP,
                    'id_plan'         => self::ID_PLAN,
                    'observacion_ins' => 'Inscripción de prueba para certificados',
                    'periodo'         => '2026-I',
                    'gestion'         => '2026',
                    'fecha_reg'       => $now,
                    'estado'          => 1,
                    'per_modificar'   => 0,
                ], 'id_ins');
                $insInsertados++;
            }

            if ($est['pagos'] === 'completo') {
                $cuotasAPagar = self::FECHAPACOS;
            } elseif ($est['pagos'] === 'parcial') {
                $cuotasAPagar = [self::FECHAPACOS[0]];
            } else {
                $cuotasAPagar = [];
            }

            foreach ($cuotasAPagar as $fpId) {
                $existePago = DB::table('t_pago')
                    ->where('id_us', $est['id_us'])
                    ->where('id_fechapago', $fpId)
                    ->where('estado', 1)
                    ->exists();

                if ($existePago) {
                    continue;
                }

                $idPago = DB::table('t_pago')->insertGetId([
                    'id_us_reg'                      => 1,
                    'num_pago'                       => 0,
                    'id_us'                          => $est['id_us'],
                    'id_mat'                         => 0,
                    'id_fechapago'                   => $fpId,
                    'monto_pagado'                   => 583,
                    'fecha_deposito'                 => '2026-02-10',
                    'dejo_boleta_deposito_original'  => 0,
                    'monto_descuento_extra'          => 0,
                    'tipo_fechapago'                 => 0,
                    'pago_extra'                     => 0,
                    'fecha_reg'                      => $now,
                    'estado'                         => 1,
                    'per_modificar'                  => 0,
                ], 'id_pago');

                DB::table('t_pago')
                    ->where('id_pago', $idPago)
                    ->update(['nro_boleta_bancaria' => 'TEST-' . $idPago]);

                $pagosInsertados++;
            }
        }

        $this->command->info("✓ Usuarios: {$usInsertados} insertados (id_us 9013–9020).");
        $this->command->info("✓ Inscripciones: {$insInsertados} insertadas en id_imp=" . self::ID_IMP . ".");
        $this->command->info("✓ Pagos: {$pagosInsertados} insertados (4 completos · 2 parciales · 2 sin pago).");
        $this->command->info('  → Alumnos elegibles para certificado: Alvarado, Barrios, Camacho, Durán (4).');
    }
}
