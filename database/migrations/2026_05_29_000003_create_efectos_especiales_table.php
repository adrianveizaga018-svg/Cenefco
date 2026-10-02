<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('efectos_especiales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100)->comment('Ej: Navidad, Año Nuevo, Aniversario CENEFCO');
            $table->string('tipo_efecto', 50)->comment('nieve, confetti, hojas, fuegos_artificiales, estrellas');
            $table->string('color_primario', 7)->nullable()->default('#ffffff')->comment('Color principal de las partículas');
            $table->string('color_secundario', 7)->nullable()->comment('Color secundario opcional');
            $table->date('fecha_inicio')->comment('Inicio de la ventana de activación');
            $table->date('fecha_fin')->comment('Fin de la ventana de activación');
            $table->unsignedSmallInteger('intensidad')->default(50)->comment('0-100: cantidad de partículas');
            $table->boolean('activo')->default(true);
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->index('activo');
            $table->index(['fecha_inicio', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('efectos_especiales');
    }
};
