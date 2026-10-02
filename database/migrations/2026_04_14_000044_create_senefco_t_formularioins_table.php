<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_formularioins', function (Blueprint $table) {
            $table->integer('id_formins');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_formularioins')->default('0');
            $table->string('nombre_formins', 200)->nullable();
            $table->string('asunto_email', 200)->nullable();
            $table->string('correo_remitente', 200)->nullable();
            $table->string('correo_control', 200)->nullable();
            $table->text('contenido_email')->nullable();
            $table->integer('id_us')->nullable()->default('0');
            $table->text('texto_envio_correcto')->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->integer('id_curso')->nullable()->default('0');
            $table->integer('id_imp')->nullable()->default('0');
            $table->primary(['id_formins', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_formularioins');
    }
};
