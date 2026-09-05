<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mdl_course', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_modcurso')->default('0');
            $table->string('fullname', 254)->nullable();
            $table->string('shortname', 255)->nullable();
            $table->integer('id_docente')->nullable();
            $table->integer('category')->nullable();
            $table->string('sigla', 200)->nullable();
            $table->string('paralelo', 200)->nullable();
            $table->string('cupo', 200)->nullable();
            $table->string('grado_academico1', 200)->nullable();
            $table->string('grado_academico2', 200)->nullable();
            $table->text('observacion_imp')->nullable();
            $table->date('imparte_fecha_inicio')->nullable()->default('2000-01-01');
            $table->date('imparte_fecha_fin')->nullable()->default('2000-01-01');
            $table->date('imparte_fecha_acta')->nullable()->default('2000-01-01');
            $table->integer('ocultar_coordinador_acta')->nullable()->default('0');
            $table->integer('id_coordinador')->nullable()->default('0');
            $table->string('grado_coordinador1', 200)->nullable();
            $table->string('grado_coordinador2', 200)->nullable();
            $table->string('titulo_personalizado', 200)->nullable();
            $table->string('subtitulo_personalizado', 200)->nullable();
            $table->string('gestion', 200)->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mdl_course');
    }
};
