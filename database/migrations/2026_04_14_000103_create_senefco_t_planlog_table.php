<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_planlog', function (Blueprint $table) {
            $table->integer('id_planlog');
            $table->integer('id_plan')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_plan')->default('0');
            $table->string('titulo', 200)->nullable();
            $table->string('convenio', 200)->nullable();
            $table->string('titulo_carta', 200)->nullable();
            $table->string('titulo_carta2', 200)->nullable();
            $table->string('anio', 200)->nullable();
            $table->string('costo', 200)->nullable();
            $table->string('nro_cuotas', 200)->nullable();
            $table->string('descuento', 200)->nullable()->default('0');
            $table->string('costo_por_cuota', 200)->nullable();
            $table->integer('id_catplan')->nullable();
            $table->string('titulo_plan', 200)->nullable();
            $table->integer('id_moodle')->nullable()->default('0');
            $table->string('id_cursomoodle', 200)->nullable();
            $table->integer('promocionar')->nullable()->default('0');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_planlog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_planlog');
    }
};
