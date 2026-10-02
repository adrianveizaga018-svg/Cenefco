<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_grupo', function (Blueprint $table) {
            $table->integer('id_grupo');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_grupo')->default('0');
            $table->integer('id_test')->nullable()->default('0');
            $table->string('siglagrupo', 200)->nullable();
            $table->string('nombregrupo', 200)->nullable();
            $table->text('espacio_laboral')->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->string('titulo', 200)->nullable();
            $table->primary(['id_grupo', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_grupo');
    }
};
