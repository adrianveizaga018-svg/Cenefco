<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_revista', function (Blueprint $table) {
            $table->integer('id_revista');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_revista')->default('0');
            $table->string('titulo_revista', 200)->nullable();
            $table->text('descripcion_revista')->nullable();
            $table->date('fecha_publicacion')->nullable()->default('2000-01-01');
            $table->string('archivo', 200)->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_revista', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_revista');
    }
};
