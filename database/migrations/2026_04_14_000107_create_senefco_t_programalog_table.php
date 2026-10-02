<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_programalog', function (Blueprint $table) {
            $table->integer('id_programalog');
            $table->integer('id_programa')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_programa')->default('0');
            $table->string('nombre_programa', 200)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('foto', 200)->nullable()->default('uno.png');
            $table->date('inicio_actividades')->nullable()->default('2000-01-01');
            $table->date('finalizacion_actividades')->nullable()->default('2000-01-01');
            $table->date('inicio_inscripciones')->nullable()->default('2000-01-01');
            $table->string('titulo_documento1', 200)->nullable();
            $table->string('documento1', 200)->nullable();
            $table->string('titulo_documento2', 200)->nullable();
            $table->string('documento2', 200)->nullable();
            $table->string('titulo_documento3', 200)->nullable();
            $table->string('documento3', 200)->nullable();
            $table->string('titulo_documento4', 200)->nullable();
            $table->string('documento4', 200)->nullable();
            $table->text('dirigido')->nullable();
            $table->text('inversion')->nullable();
            $table->text('requisitos')->nullable();
            $table->text('Creditaje')->nullable();
            $table->text('objetivo')->nullable();
            $table->text('nota')->nullable();
            $table->integer('id_tipoprograma')->nullable()->default('0');
            $table->string('url_video', 200)->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_programalog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_programalog');
    }
};
