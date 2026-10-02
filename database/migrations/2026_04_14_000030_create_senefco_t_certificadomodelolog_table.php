<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_certificadomodelolog', function (Blueprint $table) {
            $table->integer('id_certmodlog');
            $table->integer('id_certmod')->default('0');
            $table->string('tipo_log', 200)->nullable();
            $table->integer('id_us_reg')->default('0');
            $table->integer('num_certmod')->default('0');
            $table->string('nombre_certificadomodelo', 200)->nullable();
            $table->text('textocertificado')->nullable();
            $table->dateTime('fecha_reg')->nullable();
            $table->tinyInteger('estado')->nullable()->default('1');
            $table->tinyInteger('per_modificar')->nullable()->default('0');
            $table->primary(['id_certmodlog', 'id_us_reg']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_certificadomodelolog');
    }
};
