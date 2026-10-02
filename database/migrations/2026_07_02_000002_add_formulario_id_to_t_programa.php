<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_programa', function (Blueprint $table) {
            $table->unsignedBigInteger('formulario_id')->nullable()->after('convenio_id');
            $table->foreign('formulario_id')->references('id')->on('web_formulario')->nullOnDelete();
        });

        Schema::table('t_programa', function (Blueprint $table) {
            $table->dropColumn('tipo_formulario_inscripcion');
        });
    }

    public function down(): void
    {
        Schema::table('t_programa', function (Blueprint $table) {
            $table->dropForeign(['formulario_id']);
            $table->dropColumn('formulario_id');
            $table->string('tipo_formulario_inscripcion', 20)->nullable()->default('preinscripcion');
        });
    }
};
