<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_articulolog', function (Blueprint $table) {
            $table->integer('id_artlog');
            $table->integer('id_art')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_art')->default('0');
            $table->string('titulo', 200)->nullable();
            $table->integer('autor')->nullable();
            $table->text('contenido')->nullable();
            $table->integer('id_cat_art')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_artlog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_articulolog');
    }
};
