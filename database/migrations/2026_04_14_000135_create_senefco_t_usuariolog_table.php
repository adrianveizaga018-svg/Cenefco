<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_usuariolog', function (Blueprint $table) {
            $table->integer('id_uslog');
            $table->integer('id_us')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_us')->default('0');
            $table->string('nombre_usuario', 100)->nullable();
            $table->string('password', 200)->nullable();
            $table->string('categoria', 200)->nullable();
            $table->string('titulo_academico', 200)->nullable();
            $table->string('titulo_academico2', 200)->nullable();
            $table->string('nombre', 100)->nullable();
            $table->string('appaterno', 100)->nullable();
            $table->string('apmaterno', 200)->nullable();
            $table->string('ci', 200)->nullable();
            $table->integer('expedido')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->integer('genero')->nullable()->default('2');
            $table->string('email', 100)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 120)->nullable();
            $table->integer('estado_civil')->nullable();
            $table->string('pais', 200)->nullable();
            $table->integer('id_universidad')->nullable()->default('0');
            $table->integer('id_carrera')->nullable()->default('0');
            $table->integer('id_prof')->nullable()->default('0');
            $table->string('foto', 200)->nullable()->default('afoto1.png');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->integer('id_niv')->nullable()->default('3');
            $table->integer('id_tipoprograma')->nullable()->default('1');
            $table->string('tipoestudiante', 200)->nullable()->default('2');
            $table->primary(['id_uslog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_usuariolog');
    }
};
