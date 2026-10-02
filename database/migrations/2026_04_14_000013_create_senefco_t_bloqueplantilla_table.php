<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_bloqueplantilla', function (Blueprint $table) {
            $table->integer('id_bloqueplantilla');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_bloqueplantilla')->default('0');
            $table->string('titulo_bloqueplantilla', 200)->nullable();
            $table->text('cod_seccion')->nullable();
            $table->text('codigo_html')->nullable();
            $table->string('numero_bloques', 200)->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_bloqueplantilla', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_bloqueplantilla');
    }
};
