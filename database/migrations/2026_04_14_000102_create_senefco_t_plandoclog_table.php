<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_plandoclog', function (Blueprint $table) {
            $table->integer('id_plandoclog');
            $table->integer('id_plandoc')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_plandoc')->default('0');
            $table->string('titulo_plandoc', 200)->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_plandoclog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_plandoclog');
    }
};
