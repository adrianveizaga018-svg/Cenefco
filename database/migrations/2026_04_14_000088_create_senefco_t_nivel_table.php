<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_nivel', function (Blueprint $table) {
            $table->double('id_niv');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_niv')->default('0');
            $table->string('titulo', 200)->nullable();
            $table->integer('codigo')->nullable()->default('0');
            $table->integer('validar_grupopermiso')->nullable()->default('0');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->integer('estado')->nullable()->default('1');
            $table->primary(['id_niv', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_nivel');
    }
};
