<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_funcionalidadformlog', function (Blueprint $table) {
            $table->integer('id_funcionalidadformlog');
            $table->integer('id_funcionalidadform')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_funcionalidadform')->default('0');
            $table->integer('id_regform')->default('0');
            $table->string('codigo_funcionalidad', 200)->nullable();
            $table->string('nombre_funcionalidad', 200)->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_funcionalidadformlog', 'id_regform', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_funcionalidadformlog');
    }
};
