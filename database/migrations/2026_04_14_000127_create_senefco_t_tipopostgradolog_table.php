<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_tipopostgradolog', function (Blueprint $table) {
            $table->integer('id_tipopostlog');
            $table->integer('id_tipopost')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_tipopost')->default('0');
            $table->integer('id_plan')->default('0');
            $table->integer('id_tipopago')->nullable();
            $table->string('descuentopostgrado', 200)->nullable()->default('0');
            $table->string('calculo_cuota', 200)->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_tipopostlog', 'id_plan', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_tipopostgradolog');
    }
};
