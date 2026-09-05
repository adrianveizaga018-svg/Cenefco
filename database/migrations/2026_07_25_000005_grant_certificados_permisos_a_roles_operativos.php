<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permisoIds = DB::table('permisos')
            ->whereIn('codigo', ['certificados.ver', 'certificados.crear', 'certificados.editar'])
            ->pluck('id', 'codigo');

        $otorgar = [
            'academica' => ['certificados.ver', 'certificados.crear', 'certificados.editar'],
            'vendedor'  => ['certificados.ver', 'certificados.crear', 'certificados.editar'],
            'pasante'   => ['certificados.ver'],
        ];

        foreach ($otorgar as $rolNombre => $codigos) {
            $rolId = DB::table('roles')->where('nombre', $rolNombre)->value('id');
            if (! $rolId) {
                continue;
            }

            foreach ($codigos as $codigo) {
                if (! isset($permisoIds[$codigo])) {
                    continue;
                }

                DB::table('roles_permisos')->insertOrIgnore([
                    'rol_id'     => $rolId,
                    'permiso_id' => $permisoIds[$codigo],
                ]);
            }
        }
    }

    public function down(): void
    {
    }
};
