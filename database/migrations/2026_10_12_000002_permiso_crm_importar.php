<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Marketing sube contactos masivos (grupos de WhatsApp, ferias) al embudo sin ver el trabajo de los vendedores. */
return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('permisos')->where('codigo', 'crm.importar')->exists()) {
            DB::table('permisos')->insert([
                'codigo'      => 'crm.importar',
                'descripcion' => 'Subir contactos al embudo de ventas desde Excel (grupos de WhatsApp, ferias, bases).',
                'modulo'      => 'Ventas (CRM)',
            ]);
        }
    }

    public function down(): void
    {
        DB::table('permisos')->where('codigo', 'crm.importar')->delete();
    }
};
