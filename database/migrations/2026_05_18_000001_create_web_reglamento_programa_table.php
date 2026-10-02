<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('web_reglamento_programa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_programa')->unique();
            $table->text('bienvenida')->nullable();
            $table->text('reglas_asistencia')->nullable();
            $table->text('reglas_evaluacion')->nullable();
            $table->text('reglas_pagos')->nullable();
            $table->text('reglas_conducta')->nullable();
            $table->text('reglas_plataformas')->nullable();
            $table->text('reglas_derechos')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_reglamento_programa');
    }
};
