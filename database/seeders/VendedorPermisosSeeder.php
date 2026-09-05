<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendedorPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $rolId = DB::table('roles')->where('nombre', 'vendedor')->value('id');
        if (! $rolId) {
            $this->command->warn('Rol "vendedor" no encontrado — nada que hacer.');
            return;
        }

        DB::table('roles')->where('id', $rolId)->update(['restringido_a_vendedor' => true]);

        $codigosPermitidos = [
            'programas.ver',
            'inscripciones.ver',
            'inscripciones.editar',
            'estudiantes.ver',
            'estudiantes.editar',
            'pagos.ver',
            'pagos.crear',
            'pagos.editar',
            'compromisos-cobro.ver',
            'compromisos-cobro.crear',
            'compromisos-cobro.editar',
        ];

        $permisoIds = DB::table('permisos')->whereIn('codigo', $codigosPermitidos)->pluck('id', 'codigo');

        $faltantes = array_diff($codigosPermitidos, $permisoIds->keys()->all());
        if (! empty($faltantes)) {
            $this->command->warn('Permisos no encontrados en el catálogo: ' . implode(', ', $faltantes));
        }

        DB::table('roles_permisos')->where('rol_id', $rolId)->delete();

        foreach ($permisoIds as $permisoId) {
            DB::table('roles_permisos')->insert(['rol_id' => $rolId, 'permiso_id' => $permisoId]);
        }

        $this->command->info("✓ Rol \"vendedor\" restringido a " . $permisoIds->count() . ' permisos.');
    }
}
