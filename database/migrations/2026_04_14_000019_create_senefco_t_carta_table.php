<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_carta', function (Blueprint $table) {
            $table->integer('id_carta');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_carta')->default('0');
            $table->integer('id_us')->nullable()->default('0');
            $table->integer('id_plan')->nullable();
            $table->string('nombresenor', 200)->nullable();
            $table->string('nombretitulo', 200)->nullable();
            $table->text('textocarta1')->nullable();
            $table->text('textocarta2')->nullable();
            $table->text('textocarta3')->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_carta', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_carta');
    }
};
