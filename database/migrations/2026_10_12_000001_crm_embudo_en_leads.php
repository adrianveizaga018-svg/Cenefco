<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Embudo de ventas (CRM) sobre la tabla de interesados que ya existía.
 * Etapas: nuevo → contactado → interesado → por_pagar → inscrito | perdido.
 * Un interesado puede llegar sin campaña de leads (portal, WhatsApp, contacto).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('campana_lead_id')->nullable()->change();
            $table->string('celular', 30)->nullable()->change();
            $table->string('ci', 30)->nullable()->after('correo');
            $table->string('origen', 30)->default('manual')->after('estado');
            $table->string('motivo_perdida', 200)->nullable()->after('origen');
            $table->unsignedBigInteger('campana_publicidad_id')->nullable()->after('programa_id');
            $table->unsignedInteger('id_us')->nullable()->after('campana_publicidad_id');   // estudiante (t_usuario)
            $table->unsignedInteger('id_ins')->nullable()->after('id_us');                  // inscripción con la que cerró
            $table->timestampTz('asignado_at')->nullable();
            $table->timestampTz('ultimo_contacto_at')->nullable();
            $table->timestampTz('proxima_accion_at')->nullable();
            $table->timestampTz('cerrado_at')->nullable();
            $table->timestampTz('alerta_enviada_at')->nullable();
            $table->index('origen');
            $table->index('id_us');
            $table->index('celular');
        });

        // "descartado" pasa a llamarse "perdido" en el embudo
        DB::table('leads')->where('estado', 'descartado')->update(['estado' => 'perdido']);

        foreach ([
            'crm.ver'        => 'Ver y trabajar su embudo de ventas (vendedor).',
            'crm.supervisar' => 'Ver el embudo y el tablero de todos los vendedores, reasignar interesados.',
        ] as $codigo => $descripcion) {
            if (! DB::table('permisos')->where('codigo', $codigo)->exists()) {
                DB::table('permisos')->insert(['codigo' => $codigo, 'descripcion' => $descripcion, 'modulo' => 'Ventas (CRM)']);
            }
        }
    }

    public function down(): void
    {
        DB::table('leads')->where('estado', 'perdido')->update(['estado' => 'descartado']);

        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['origen']);
            $table->dropIndex(['id_us']);
            $table->dropIndex(['celular']);
            $table->dropColumn([
                'ci', 'origen', 'motivo_perdida', 'campana_publicidad_id', 'id_us', 'id_ins',
                'asignado_at', 'ultimo_contacto_at', 'proxima_accion_at', 'cerrado_at', 'alerta_enviada_at',
            ]);
        });
        DB::table('permisos')->whereIn('codigo', ['crm.ver', 'crm.supervisar'])->delete();
    }
};
