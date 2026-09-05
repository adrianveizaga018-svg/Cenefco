<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_fotolog', function (Blueprint $table) {
            $table->integer('id_fotolog');
            $table->integer('id_foto')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_foto')->default('0');
            $table->string('titulo_foto', 200)->nullable();
            $table->text('descripcion_foto')->nullable();
            $table->string('foto', 200)->nullable()->default('g0.jpg');
            $table->date('fecha_foto')->nullable()->default('2000-01-01');
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_fotolog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_fotolog');
    }
};
