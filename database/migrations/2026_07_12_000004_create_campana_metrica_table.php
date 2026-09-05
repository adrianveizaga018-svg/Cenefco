<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campana_metrica', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campana_publicidad_id');
            $table->date('fecha_corte');
            $table->unsignedInteger('alcance')->nullable();
            $table->unsignedInteger('impresiones')->nullable();
            $table->decimal('frecuencia', 6, 2)->nullable();
            $table->unsignedInteger('clics_enlace')->nullable();
            $table->decimal('ctr', 6, 3)->nullable();
            $table->decimal('cpc', 8, 2)->nullable();
            $table->decimal('cpm', 8, 2)->nullable();
            $table->unsignedInteger('resultados')->nullable();
            $table->string('tipo_resultado', 100)->nullable();
            $table->decimal('costo_por_resultado', 8, 2)->nullable();
            $table->decimal('gasto_periodo', 10, 2)->nullable();
            $table->string('fuente', 30)->default('manual');
            $table->text('notas')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('campana_publicidad_id')->references('id')->on('campana_publicidad');
            $table->index('fecha_corte');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campana_metrica');
    }
};
