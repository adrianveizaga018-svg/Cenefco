<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_compromiso_cobro_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('compromiso_cobro_id');
            $table->string('tipo_evento', 20);
            $table->date('fecha_anterior')->nullable();
            $table->date('fecha_nueva')->nullable();
            $table->string('motivo', 30)->nullable();
            $table->text('observacion')->nullable();
            $table->unsignedBigInteger('registrado_por');
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('compromiso_cobro_id')->references('id')->on('web_compromiso_cobro')->cascadeOnDelete();
            $table->index('compromiso_cobro_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_compromiso_cobro_log');
    }
};
