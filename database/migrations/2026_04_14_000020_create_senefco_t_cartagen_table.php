<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_cartagen', function (Blueprint $table) {
            $table->integer('id_cartagen');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_carta')->default('0');
            $table->integer('id_us')->nullable()->default('0');
            $table->integer('id_cartamod')->nullable();
            $table->text('textocarta')->nullable();
            $table->text('textocarta1')->nullable();
            $table->text('textocarta3')->nullable();
            $table->integer('usar_encabezado_pie_estandar')->nullable()->default('0');
            $table->integer('cp_nro_contrato')->nullable()->default('0');
            $table->string('cp_gestion_contrato', 200)->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_cartagen', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_cartagen');
    }
};
