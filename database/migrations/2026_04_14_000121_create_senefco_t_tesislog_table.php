<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_tesislog', function (Blueprint $table) {
            $table->integer('id_tesislog');
            $table->integer('id_tesis')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_tesis')->default('0');
            $table->string('titulo_tesis', 200)->nullable();
            $table->text('descripcion_tesis')->nullable();
            $table->date('fecha_publicacion')->nullable()->default('2000-01-01');
            $table->string('autor', 200)->nullable();
            $table->integer('tipo_tesis')->nullable()->default('0');
            $table->string('archivo', 200)->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_tesislog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_tesislog');
    }
};
