<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_historial', function (Blueprint $table) {
            $table->integer('id_historial');
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_historial')->default('0');
            $table->integer('id_us')->nullable()->default('0');
            $table->date('fecha')->nullable()->default('1910-01-01');
            $table->integer('id_tiporeferencia')->nullable()->default('0');
            $table->integer('id_tipohistorial')->nullable()->default('0');
            $table->string('documento_digital', 200)->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_historial', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_historial');
    }
};
