<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AutoridadesSeeder extends Seeder
{
    public function run(): void
    {
        $dgId = DB::table('secretarias')->where('sigla', 'DG')->value('id');
        $saId = DB::table('secretarias')->where('sigla', 'SA')->value('id');
        $safId = DB::table('secretarias')->where('sigla', 'SAF')->value('id');

        $autoridades = [
            [
                'secretaria_id'      => $dgId,
                'nombre'             => 'Director',
                'apellido'           => 'General',
                'cargo'              => 'Director General',
                'tipo'               => 'director',
                'perfil_profesional' => 'Máxima autoridad ejecutiva de CENEFCO.',
                'orden'              => 1,
                'activo'             => true,
            ],
            [
                'secretaria_id'      => $saId,
                'nombre'             => 'Responsable',
                'apellido'           => 'Académico',
                'cargo'              => 'Secretario Académico',
                'tipo'               => 'secretario',
                'perfil_profesional' => 'Gestión y coordinación de la oferta académica institucional.',
                'orden'              => 2,
                'activo'             => true,
            ],
            [
                'secretaria_id'      => $safId,
                'nombre'             => 'Responsable',
                'apellido'           => 'Administrativo',
                'cargo'              => 'Secretario Administrativo y Financiero',
                'tipo'               => 'secretario',
                'perfil_profesional' => 'Administración de recursos humanos y financieros.',
                'orden'              => 3,
                'activo'             => true,
            ],
        ];

        foreach ($autoridades as $aut) {
            $slug = Str::slug($aut['nombre'].' '.$aut['apellido'].' '.$aut['cargo']);
            $uniqueSlug = $slug.'-'.uniqid();
            
            $exists = DB::table('autoridades')->where('cargo', $aut['cargo'])->exists();
            if (! $exists) {
                DB::table('autoridades')->insertOrIgnore(array_merge($aut, ['slug' => $uniqueSlug]));
            }
        }
    }
}
