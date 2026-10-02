<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_usuarioprogramalog', function (Blueprint $table) {
            $table->integer('id_usuarioprogramalog');
            $table->integer('id_usuarioprograma')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_usuarioprograma')->default('0');
            $table->integer('id_us')->default('0');
            $table->integer('id_programa')->nullable()->default('0');
            $table->integer('id_tipoprograma')->nullable()->default('0');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_usuarioprogramalog', 'id_us', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_usuarioprogramalog');
    }
};
