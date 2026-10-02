<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_testlog', function (Blueprint $table) {
            $table->integer('id_testlog');
            $table->integer('id_test')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_test')->default('0');
            $table->string('nombretest', 200)->nullable();
            $table->string('asunto_email', 200)->nullable();
            $table->string('correo_remitente', 200)->nullable();
            $table->string('correo_control', 200)->nullable();
            $table->text('contenido_email')->nullable();
            $table->integer('id_us')->nullable()->default('0');
            $table->text('texto_envio_correcto')->nullable();
            $table->text('descripcion_test')->nullable();
            $table->text('introduccion_test')->nullable();
            $table->string('habilitar_test', 200)->nullable()->default('Si');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_testlog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_testlog');
    }
};
