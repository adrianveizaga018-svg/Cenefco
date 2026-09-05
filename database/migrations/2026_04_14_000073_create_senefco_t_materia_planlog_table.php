<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_materia_planlog', function (Blueprint $table) {
            $table->integer('id_matplanlog');
            $table->integer('id_matplan')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_mp')->default('0');
            $table->integer('id_mat')->nullable();
            $table->integer('id_plan')->nullable();
            $table->string('carga_horaria_plan', 200)->nullable()->default('0');
            $table->integer('id_preesp')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_matplanlog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_materia_planlog');
    }
};
