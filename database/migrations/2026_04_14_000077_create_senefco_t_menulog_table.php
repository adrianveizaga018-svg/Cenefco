<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_menulog', function (Blueprint $table) {
            $table->integer('id_menlog');
            $table->integer('id_men')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_cat')->default('0');
            $table->integer('id_mod')->default('0');
            $table->string('nombre_cat', 200)->nullable();
            $table->string('enlace', 200)->nullable();
            $table->integer('sub_cat')->nullable();
            $table->integer('id_niv')->nullable();
            $table->text('id_niv_exm')->nullable();
            $table->string('acceso', 200)->nullable();
            $table->string('icono', 200)->nullable();
            $table->string('ancla', 200)->nullable();
            $table->string('reglavista_clase', 200)->nullable();
            $table->text('xgrupos')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_menlog', 'id_mod', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_menulog');
    }
};
