<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_menu', function (Blueprint $table) {
            $table->double('id_men');
            $table->integer('id_mod')->default('0');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_cat')->default('0');
            $table->string('nombre_cat', 200)->nullable();
            $table->string('sub_cat', 200)->nullable()->default('');
            $table->string('id_niv', 200)->nullable()->default('');
            $table->text('id_niv_exm')->nullable();
            $table->string('acceso', 200)->nullable();
            $table->string('icono', 200)->nullable();
            $table->string('ancla', 200)->nullable();
            $table->string('reglavista_clase', 200)->nullable();
            $table->text('xgrupos')->nullable();
            $table->string('enlace', 200)->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->integer('estado')->nullable()->default('1');
            $table->primary(['id_men', 'id_mod', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_menu');
    }
};
