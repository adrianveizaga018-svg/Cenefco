<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_notalog', function (Blueprint $table) {
            $table->integer('id_notlog');
            $table->integer('id_not')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_not')->default('0');
            $table->string('periodo', 200)->nullable();
            $table->string('gestion', 10)->nullable();
            $table->integer('id_imp')->nullable()->default('0');
            $table->integer('id_us')->nullable();
            $table->integer('id_mat')->nullable();
            $table->integer('nota')->nullable();
            $table->integer('nota_seg')->nullable()->default('0');
            $table->string('paralelo', 200)->nullable();
            $table->integer('mostrarcert_notas')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_notlog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_notalog');
    }
};
