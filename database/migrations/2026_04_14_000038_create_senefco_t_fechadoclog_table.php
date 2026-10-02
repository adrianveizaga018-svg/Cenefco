<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_fechadoclog', function (Blueprint $table) {
            $table->integer('id_fechadoclog');
            $table->integer('id_fechadoc')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_fechadoc')->default('0');
            $table->string('nro_doc', 200)->nullable()->default('1');
            $table->integer('id_plandoc')->default('0');
            $table->string('tipo_documento', 200)->nullable();
            $table->date('fecha_inicio')->nullable()->default('2000-01-01');
            $table->date('fecha_fin')->nullable()->default('2000-01-01');
            $table->integer('obligatorio')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_fechadoclog', 'id_plandoc', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_fechadoclog');
    }
};
