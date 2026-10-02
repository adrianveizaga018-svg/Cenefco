<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre_completo', 200);
            $table->string('cargo', 150);
            $table->decimal('sueldo_mensual', 10, 2);
            $table->string('ci', 20)->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestampTz('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleado');
    }
};
