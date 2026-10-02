<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_modulolog', function (Blueprint $table) {
            $table->integer('id_modlog');
            $table->integer('id_mod')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_mod')->default('0');
            $table->string('titulo', 200)->nullable();
            $table->integer('mostrar_titulo')->nullable()->default('1');
            $table->string('posicion', 200)->nullable();
            $table->string('tipo', 200)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->integer('id_niv')->nullable();
            $table->text('id_niv_ex')->nullable();
            $table->integer('usar_nivel_global')->nullable()->default('1');
            $table->string('acceso', 200)->nullable();
            $table->integer('asignacion')->nullable();
            $table->text('menu_asignado')->nullable();
            $table->string('clase_php', 200)->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_modlog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_modulolog');
    }
};
