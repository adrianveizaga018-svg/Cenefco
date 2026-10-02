<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_boletin', function (Blueprint $table) {
            $table->integer('id_boletin');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_boletin')->default('0');
            $table->string('titulo_pagina', 200)->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->timestampTz('updated_at')->nullable();
            $table->timestampTz('deleted_at')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->string('titulo_boletin', 200)->nullable();
            $table->string('slug', 300)->nullable()->unique();
            $table->text('descripcion_boletin')->nullable();
            $table->string('imagen_url', 255)->nullable();
            $table->timestampTz('fecha_publicacion')->nullable();
            $table->string('meta_titulo', 300)->nullable();
            $table->string('meta_descripcion', 500)->nullable();
            $table->integer('vistas')->default(0);
            $table->primary(['id_boletin', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_boletin');
    }
};
