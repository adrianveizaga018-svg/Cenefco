<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_articulo', function (Blueprint $table) {
            $table->integer('id_art');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_art')->default('0');
            $table->string('titulo', 200)->nullable();
            $table->string('slug', 300)->nullable()->unique();
            $table->string('entradilla', 500)->nullable();
            $table->integer('autor')->nullable();
            $table->text('contenido')->nullable();
            $table->string('imagen_principal_url', 255)->nullable();
            $table->string('imagen_alt', 255)->nullable();
            $table->boolean('destacada')->default(false);
            $table->timestampTz('fecha_publicacion')->nullable();
            $table->integer('vistas')->default(0);
            $table->string('meta_titulo', 300)->nullable();
            $table->string('meta_descripcion', 500)->nullable();
            $table->string('estado_web', 50)->default('borrador');
            $table->integer('id_cat_art')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->timestampTz('updated_at')->nullable();
            $table->timestampTz('deleted_at')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_art', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_articulo');
    }
};
