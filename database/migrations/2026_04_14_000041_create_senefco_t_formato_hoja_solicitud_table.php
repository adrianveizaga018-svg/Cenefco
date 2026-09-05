<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_formato_hoja_solicitud', function (Blueprint $table) {
            $table->integer('id_formato_hoja_solicitud');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_formato_hoja_solicitud')->default('0');
            $table->string('titulo', 200)->nullable();
            $table->text('posiciones')->nullable();
            $table->text('txtdirigidoa')->nullable();
            $table->text('txtasunto')->nullable();
            $table->text('txtcontenido1')->nullable();
            $table->string('txtpie1', 200)->nullable();
            $table->string('txtpie2', 200)->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_formato_hoja_solicitud', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_formato_hoja_solicitud');
    }
};
