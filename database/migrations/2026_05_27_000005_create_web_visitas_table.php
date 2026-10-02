<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_visitas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('session_id', 36);
            $table->string('url', 2000);
            $table->string('ruta', 500);
            $table->string('titulo', 300)->nullable();
            $table->string('referrer', 2000)->nullable();
            $table->string('pais', 100)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('dispositivo', 20)->nullable();
            $table->string('navegador', 50)->nullable();
            $table->string('so', 50)->nullable();
            $table->unsignedSmallInteger('duracion_seg')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index('session_id');
            $table->index('ruta');
            $table->index('created_at');
            $table->index('pais');
            $table->index('dispositivo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_visitas');
    }
};
