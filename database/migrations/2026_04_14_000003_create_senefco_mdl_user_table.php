<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mdl_user', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_modusuario')->default('0');
            $table->string('nombre_usuario', 100)->nullable();
            $table->string('password', 200)->nullable();
            $table->string('nombre', 100)->nullable();
            $table->string('appaterno', 100)->nullable();
            $table->string('apmaterno', 200)->nullable();
            $table->string('ci', 200)->nullable();
            $table->integer('expedido')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 120)->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mdl_user');
    }
};
