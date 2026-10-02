<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_seccionbloque', function (Blueprint $table) {
            $table->integer('id_seccionbloque');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_seccionbloque')->default('0');
            $table->integer('id_bloqueajustable')->default('0');
            $table->text('texto_seccion')->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_seccionbloque', 'id_bloqueajustable', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_seccionbloque');
    }
};
