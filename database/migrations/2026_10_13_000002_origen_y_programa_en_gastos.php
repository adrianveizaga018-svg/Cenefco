<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Los gastos que el sistema registra solo (pagos a docentes, publicidad de Facebook, planillas)
 * guardan de dónde vienen, para no cargarlos dos veces y poder actualizarlos o anularlos.
 * id_programa permite ver la ganancia de cada programa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gasto', function (Blueprint $table) {
            $table->string('origen', 30)->default('manual');   // manual | honorario_docente | publicidad_meta | planilla
            $table->string('origen_ref', 60)->nullable();      // id del pago a docente, "campaña:AAAA-MM", id de planilla
            $table->unsignedBigInteger('id_programa')->nullable();
            $table->index(['origen', 'origen_ref']);
            $table->index('id_programa');
        });

        if (Schema::hasTable('planilla') && Schema::hasColumn('planilla', 'gasto_id')) {
            foreach (DB::table('planilla')->whereNotNull('gasto_id')->get(['id', 'gasto_id']) as $p) {
                DB::table('gasto')->where('id', $p->gasto_id)->update(['origen' => 'planilla', 'origen_ref' => (string) $p->id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('gasto', function (Blueprint $table) {
            $table->dropIndex(['origen', 'origen_ref']);
            $table->dropIndex(['id_programa']);
            $table->dropColumn(['origen', 'origen_ref', 'id_programa']);
        });
    }
};
