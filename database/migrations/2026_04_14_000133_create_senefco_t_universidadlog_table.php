<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_universidadlog', function (Blueprint $table) {
            $table->integer('id_universidadlog');
            $table->integer('id_universidad')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_universidad')->default('0');
            $table->string('nombre_universidad', 200)->nullable();
            $table->integer('id_ciudad')->nullable()->default('0');
            $table->integer('id_tipouniversidad')->nullable()->default('0');
            $table->string('campox', 200)->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_universidadlog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_universidadlog');
    }
};
