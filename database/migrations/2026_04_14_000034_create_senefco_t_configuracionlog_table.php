<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_configuracionlog', function (Blueprint $table) {
            $table->integer('id_conflog');
            $table->integer('id_conf')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_conf')->default('0');
            $table->string('gestion', 10)->nullable();
            $table->string('periodo_est', 200)->nullable();
            $table->string('gestion_est', 200)->nullable();
            $table->string('max_materias_cursar', 200)->nullable()->default('6');
            $table->integer('id_plan')->nullable();
            $table->integer('id_plan_anterior')->nullable();
            $table->string('periodo_doc', 200)->nullable();
            $table->string('gestion_doc', 200)->nullable();
            $table->string('correlativo', 200)->nullable();
            $table->string('nombre_kardista', 200)->nullable();
            $table->string('nombre_director', 200)->nullable();
            $table->string('titulo_carrera', 200)->nullable();
            $table->string('descripcion_resolucion', 200)->nullable();
            $table->string('cod_codigo', 200)->nullable();
            $table->string('lugar_x', 200)->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->string('carrera', 200)->nullable();
            $table->string('area', 200)->nullable();
            $table->string('periodo', 30)->nullable();
            $table->primary(['id_conflog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_configuracionlog');
    }
};
