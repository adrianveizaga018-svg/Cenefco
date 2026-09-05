<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_cartamodelo', function (Blueprint $table) {
            $table->integer('id_cartamod');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_cartamod')->default('0');
            $table->string('nombremodelo', 200)->nullable();
            $table->text('textocarta')->nullable();
            $table->text('textocarta1')->nullable();
            $table->text('textocarta3')->nullable();
            $table->text('texto_carta')->nullable();
            $table->integer('usar_encabezado_pie_estandar')->nullable()->default('0');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_cartamod', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_cartamodelo');
    }
};
