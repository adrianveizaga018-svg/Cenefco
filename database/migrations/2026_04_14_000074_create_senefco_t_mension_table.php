<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_mension', function (Blueprint $table) {
            $table->integer('id_mension');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_mension')->default('0');
            $table->string('titulo_mension', 50)->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_mension', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_mension');
    }
};
