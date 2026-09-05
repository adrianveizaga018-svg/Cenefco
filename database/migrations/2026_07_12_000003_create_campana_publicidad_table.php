<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campana_publicidad', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('programa_id')->nullable();
            $table->string('proposito', 50)->default('curso');
            $table->string('nombre', 200);
            $table->string('plataforma', 50);
            $table->string('objetivo', 100)->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->string('estado', 30)->default('planificada');
            $table->decimal('presupuesto_planificado', 10, 2)->nullable();
            $table->string('moneda', 10)->default('BOB');
            $table->string('id_campana_externa', 100)->nullable();
            $table->string('responsable', 150)->nullable();
            $table->text('notas')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestampTz('deleted_at')->nullable();

            $table->index('programa_id');
            $table->index('plataforma');
            $table->index(['fecha_inicio', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campana_publicidad');
    }
};
