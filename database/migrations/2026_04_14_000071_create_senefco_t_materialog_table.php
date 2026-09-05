<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_materialog', function (Blueprint $table) {
            $table->integer('id_matlog');
            $table->integer('id_mat')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_mat')->default('0');
            $table->string('sigla', 200)->nullable();
            $table->string('nombremat', 200)->nullable();
            $table->string('nombre', 200)->nullable();
            $table->string('semestre', 200)->nullable()->default('1');
            $table->integer('modalidad')->nullable()->default('0');
            $table->string('carga_horaria', 200)->nullable()->default('0');
            $table->text('observacion')->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_matlog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_materialog');
    }
};
