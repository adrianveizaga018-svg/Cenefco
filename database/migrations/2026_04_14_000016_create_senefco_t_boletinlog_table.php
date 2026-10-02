<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_boletinlog', function (Blueprint $table) {
            $table->integer('id_boletinlog');
            $table->integer('id_boletin')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_boletin')->default('0');
            $table->string('titulo_pagina', 200)->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->string('titulo_boletin', 200)->nullable();
            $table->text('descripcion_boletin')->nullable();
            $table->primary(['id_boletinlog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_boletinlog');
    }
};
