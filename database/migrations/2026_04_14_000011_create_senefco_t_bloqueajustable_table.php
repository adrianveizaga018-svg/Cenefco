<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_bloqueajustable', function (Blueprint $table) {
            $table->integer('id_bloqueajustable');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_bloqueajustable')->default('0');
            $table->integer('id_pagina')->nullable()->default('0');
            $table->integer('id_bloqueplantilla')->nullable()->default('0');
            $table->string('referencia_id_bloqueajustable', 200)->nullable();
            $table->integer('permitir_agregarseccion')->nullable()->default('1');
            $table->string('numero_secciones', 200)->nullable()->default('1');
            $table->integer('permitir_configurarbloque')->nullable()->default('1');
            $table->integer('permitir_gestionarseccion')->nullable()->default('1');
            $table->string('bd_tabla', 200)->nullable();
            $table->text('bd_campos')->nullable();
            $table->string('bd_condicion', 200)->nullable();
            $table->integer('permitir_configurarseccion')->nullable()->default('1');
            $table->text('texto_bloque')->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->string('nombre_bloqueajustable', 200)->nullable();
            $table->primary(['id_bloqueajustable', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_bloqueajustable');
    }
};
