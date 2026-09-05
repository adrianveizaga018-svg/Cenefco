<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planilla_detalle', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('planilla_id');
            $table->unsignedBigInteger('empleado_id');
            $table->string('nombre_completo', 200);
            $table->string('cargo', 150);
            $table->decimal('monto', 10, 2);
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('planilla_id')->references('id')->on('planilla')->cascadeOnDelete();
            $table->foreign('empleado_id')->references('id')->on('empleado');
            $table->index('planilla_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planilla_detalle');
    }
};
