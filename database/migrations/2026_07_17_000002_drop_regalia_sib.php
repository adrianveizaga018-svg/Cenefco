<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $permisoIds = DB::table('permisos')->where('modulo', 'regalia-sib')->pluck('id');
        DB::table('roles_permisos')->whereIn('permiso_id', $permisoIds)->delete();
        DB::table('permisos')->where('modulo', 'regalia-sib')->delete();

        Schema::dropIfExists('web_regalia_sib');

        Schema::table('t_programa', function (Blueprint $table) {
            $table->dropColumn('avalado_sib');
        });
    }

    public function down(): void
    {
        Schema::table('t_programa', function (Blueprint $table) {
            $table->boolean('avalado_sib')->default(false)->after('costo_monto');
        });

        Schema::create('web_regalia_sib', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('certificado_id');
            $table->unsignedInteger('imparte_id');
            $table->string('estudiante_nombre', 300);
            $table->string('programa_nombre', 300);
            $table->decimal('precio_curso', 10, 2);
            $table->decimal('porcentaje', 5, 2)->default(30.00);
            $table->decimal('monto', 10, 2);
            $table->string('estado', 20)->default('pendiente');
            $table->unsignedBigInteger('gasto_id')->nullable();
            $table->timestampTz('confirmado_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('certificado_id')->references('id')->on('t_certificado')->onDelete('restrict');
            $table->foreign('gasto_id')->references('id')->on('gasto')->onDelete('restrict');
            $table->unique('certificado_id');
            $table->index(['estado']);
            $table->index(['imparte_id']);
        });
    }
};
