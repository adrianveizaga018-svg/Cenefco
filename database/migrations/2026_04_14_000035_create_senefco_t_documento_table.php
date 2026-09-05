<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_documento', function (Blueprint $table) {
            $table->integer('id_documento');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_documento')->default('0');
            $table->integer('id_us')->nullable()->default('0');
            $table->integer('id_fechapago')->nullable()->default('0');
            $table->integer('id_fechadoc')->nullable()->default('0');
            $table->date('fecha_dejo_fisico')->nullable()->default('2000-01-01');
            $table->integer('dejo_documento_fisico')->nullable()->default('0');
            $table->string('documento_digital', 200)->nullable();
            $table->text('observacion_doc')->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_documento', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_documento');
    }
};
