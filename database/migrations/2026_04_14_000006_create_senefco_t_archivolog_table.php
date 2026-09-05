<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_archivolog', function (Blueprint $table) {
            $table->integer('id_archlog');
            $table->integer('id_arch')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_arch')->default('0');
            $table->string('nombre', 200)->nullable();
            $table->string('extensiones_permitidas', 300)->nullable();
            $table->integer('peso_maximo')->nullable();
            $table->string('directorio_arch', 200)->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_archlog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_archivolog');
    }
};
