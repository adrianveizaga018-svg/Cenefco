<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_hora', function (Blueprint $table) {
            $table->integer('id_hor');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_hor')->default('0');
            $table->string('titulo', 200)->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_hor', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_hora');
    }
};
