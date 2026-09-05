<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_hojaevaluacion', function (Blueprint $table) {
            $table->integer('id_hojaevaluacion');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_hojaevaluacion')->default('0');
            $table->integer('id_us')->nullable()->default('0');
            $table->text('titulo_trabajo')->nullable();
            $table->integer('id_us_tut')->nullable()->default('0');
            $table->string('nombre_secretario', 200)->nullable();
            $table->string('lugar_y_fecha', 200)->nullable();
            $table->integer('id_us_tribpres')->nullable()->default('0');
            $table->integer('id_us_trib1')->nullable()->default('0');
            $table->integer('id_us_trib2')->nullable()->default('0');
            $table->integer('id_us_trib3')->nullable()->default('0');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_hojaevaluacion', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_hojaevaluacion');
    }
};
