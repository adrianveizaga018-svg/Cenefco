<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuarioLegacySeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        
        $niveles = [
            ['id_niv' => 1, 'titulo' => 'Administrador', 'codigo' => 1],
            ['id_niv' => 2, 'titulo' => 'Docente',        'codigo' => 2],
            ['id_niv' => 3, 'titulo' => 'Estudiante',     'codigo' => 3],
        ];

        foreach ($niveles as $n) {
            DB::table('t_nivel')->insertOrIgnore([
                'id_niv'     => $n['id_niv'],
                'id_us_reg'  => 1,
                'num_niv'    => $n['id_niv'],
                'titulo'     => $n['titulo'],
                'codigo'     => $n['codigo'],
                'validar_grupopermiso' => 0,
                'fecha_reg'  => $now,
                'estado'     => 1,
                'per_modificar' => 0,
            ]);
        }

        
        
        
        $usuarios = [
            ['id_us' => 1,    'nombre' => 'Carlos',    'appaterno' => 'Mamani',    'apmaterno' => 'Quispe',   'ci' => '1234567',  'email' => 'carlos.mamani@gmail.com',    'celular' => '70112233', 'ciudad' => 'La Paz',     'id_niv' => 3],
            ['id_us' => 2,    'nombre' => 'Ana',       'appaterno' => 'Flores',    'apmaterno' => 'Condori',  'ci' => '2345678',  'email' => 'ana.flores@hotmail.com',      'celular' => '71234567', 'ciudad' => 'Cochabamba', 'id_niv' => 3],
            ['id_us' => 3,    'nombre' => 'Luis',      'appaterno' => 'Gutierrez', 'apmaterno' => 'Vargas',   'ci' => '3456789',  'email' => 'luis.gutierrez@gmail.com',    'celular' => '72345678', 'ciudad' => 'Santa Cruz', 'id_niv' => 3],
            ['id_us' => 4,    'nombre' => 'María',     'appaterno' => 'Choque',    'apmaterno' => 'Apaza',    'ci' => '4567890',  'email' => 'maria.choque@yahoo.com',      'celular' => '73456789', 'ciudad' => 'Oruro',      'id_niv' => 3],
            ['id_us' => 5,    'nombre' => 'Jorge',     'appaterno' => 'Torrez',    'apmaterno' => 'Limachi',  'ci' => '5678901',  'email' => 'jorge.torrez@gmail.com',      'celular' => '74567890', 'ciudad' => 'Potosí',     'id_niv' => 3],
            ['id_us' => 9001, 'nombre' => 'Alejandro', 'appaterno' => 'Vargas',    'apmaterno' => 'Mendoza',  'ci' => '6001001',  'email' => 'alejandro.vargas@cenefco.bo', 'celular' => '70011001', 'ciudad' => 'La Paz',     'id_niv' => 2],
            ['id_us' => 9002, 'nombre' => 'Patricia',  'appaterno' => 'Morales',   'apmaterno' => 'Salinas',  'ci' => '6002002',  'email' => 'patricia.morales@cenefco.bo', 'celular' => '70022002', 'ciudad' => 'La Paz',     'id_niv' => 2],
            ['id_us' => 9003, 'nombre' => 'Fernando',  'appaterno' => 'Ramos',     'apmaterno' => 'Ortega',   'ci' => '6003003',  'email' => 'fernando.ramos@cenefco.bo',   'celular' => '70033003', 'ciudad' => 'La Paz',     'id_niv' => 2],
            ['id_us' => 9004, 'nombre' => 'Claudia',   'appaterno' => 'Soria',     'apmaterno' => 'Pedraza',  'ci' => '6004004',  'email' => 'claudia.soria@cenefco.bo',    'celular' => '70044004', 'ciudad' => 'La Paz',     'id_niv' => 2],
            ['id_us' => 9005, 'nombre' => 'Roberto',   'appaterno' => 'Espinoza',  'apmaterno' => 'Cáceres',  'ci' => '6005005',  'email' => 'roberto.espinoza@cenefco.bo', 'celular' => '70055005', 'ciudad' => 'La Paz',     'id_niv' => 2],
        ];

        foreach ($usuarios as $u) {
            $exists = DB::table('t_usuario')
                ->where('id_us', $u['id_us'])
                ->where('id_us_reg', 1)
                ->exists();
            if ($exists) continue;

            DB::table('t_usuario')->insert([
                'id_us'           => $u['id_us'],
                'id_us_reg'       => 1,
                'num_us'          => $u['id_us'],
                'tipoestudiante'  => $u['id_niv'] === 2 ? '1' : '2',
                'nombre_usuario'  => strtolower($u['nombre'] . '.' . $u['appaterno']),
                'password'        => bcrypt('Cenefco2025!'),
                'categoria'       => null,
                'titulo_academico'=> $u['id_niv'] === 2 ? 'Lic.' : null,
                'appaterno'       => $u['appaterno'],
                'apmaterno'       => $u['apmaterno'],
                'nombre'          => $u['nombre'],
                'ci'              => $u['ci'],
                'expedido'        => 1,
                'telefono'        => $u['celular'],
                'celular'         => $u['celular'],
                'genero'          => 2,
                'email'           => $u['email'],
                'ciudad'          => $u['ciudad'],
                'id_universidad'  => 0,
                'id_carrera'      => 0,
                'foto'            => 'afoto1.png',
                'fecha_reg'       => $now,
                'estado'          => 1,
                'per_modificar'   => 0,
                'id_niv'          => $u['id_niv'],
                'id_tipoprograma' => 1,
            ]);
        }

        $totalNiveles  = DB::table('t_nivel')->count();
        $totalUsuarios = DB::table('t_usuario')->count();
        $this->command->info("✓ {$totalNiveles} niveles en t_nivel.");
        $this->command->info("✓ {$totalUsuarios} usuarios en t_usuario.");
    }
}
