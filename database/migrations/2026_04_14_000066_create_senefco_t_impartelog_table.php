<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_impartelog', function (Blueprint $table) {
            $table->integer('id_implog');
            $table->integer('id_imp')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_imp')->default('0');
            $table->string('version', 30)->nullable();
            $table->integer('id_us')->nullable();
            $table->integer('id_mat')->nullable();
            $table->string('paralelo', 200)->nullable();
            $table->string('cupo', 200)->nullable();
            $table->text('observacion_imp')->nullable();
            $table->date('imparte_fecha_inicio')->nullable()->default('2000-01-01');
            $table->date('imparte_fecha_fin')->nullable()->default('2000-01-01');
            $table->date('imparte_fecha_acta')->nullable()->default('2000-01-01');
            $table->integer('id_moodle')->nullable()->default('0');
            $table->string('imp_moodle_id_course', 200)->nullable()->default('0');
            $table->integer('id_certificado_aprobacion')->nullable()->default('0');
            $table->integer('id_certificado_participacion')->nullable()->default('0');
            $table->string('nota_min_aprobacion', 200)->nullable()->default('0');
            $table->string('nota_min_participacion', 200)->nullable()->default('0');
            $table->integer('mostrar_fecha_acta')->nullable()->default('0');
            $table->integer('mostrar_firma1')->nullable()->default('1');
            $table->integer('id_us_firma1')->nullable()->default('0');
            $table->string('grado_academico_firma1', 200)->nullable();
            $table->string('rol_firma1', 200)->nullable()->default('DOCENTE');
            $table->integer('mostrar_firma2')->nullable()->default('0');
            $table->integer('id_us_firma2')->nullable()->default('0');
            $table->string('grado_academico_firma2', 200)->nullable();
            $table->string('rol_firma2', 200)->nullable();
            $table->string('titulo_personalizado1', 200)->nullable();
            $table->string('titulo_personalizado2', 200)->nullable()->default('ACTA DE NOTAS FINALES');
            $table->string('titulo_personalizado3', 200)->nullable();
            $table->string('titulo_personalizado4', 200)->nullable();
            $table->integer('mostrar_paralelo')->nullable()->default('0');
            $table->string('titulo_personalizado', 200)->nullable();
            $table->string('subtitulo_personalizado', 200)->nullable();
            $table->string('id_cursomoodle', 200)->nullable()->default('0');
            $table->integer('inscripcion_auto')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->string('periodo', 30)->nullable();
            $table->string('gestion', 10)->nullable();
            $table->primary(['id_implog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_impartelog');
    }
};
