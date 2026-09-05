<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $adminMinusculaId = DB::table('roles')->where('nombre', 'admin')->value('id');
        if (! $adminMinusculaId) {
            return;
        }

        $adminMayusculaId = DB::table('roles')->where('nombre', 'Admin')->value('id');

        if ($adminMayusculaId) {
            $usuarios = DB::table('usuarios_roles')->where('rol_id', $adminMinusculaId)->get();
            foreach ($usuarios as $ur) {
                DB::table('usuarios_roles')->updateOrInsert(
                    ['usuario_id' => $ur->usuario_id, 'rol_id' => $adminMayusculaId],
                    ['asignado_at' => $ur->asignado_at, 'asignado_por' => $ur->asignado_por]
                );
            }
        }

        DB::table('roles')->where('id', $adminMinusculaId)->delete();
    }

    public function down(): void
    {

    }
};
