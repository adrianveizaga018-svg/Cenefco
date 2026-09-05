<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_cert_plantilla', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 300);
            $table->string('tipo', 50)->default('aprobacion');
            $table->string('imagen_url', 500);
            $table->integer('ancho_px')->default(3508);
            $table->integer('alto_px')->default(2480);
            $table->string('orientacion', 20)->default('horizontal');
            $table->string('formato_salida', 10)->default('jpg');
            $table->integer('calidad_jpg')->default(95);
            $table->string('fuente_default', 100)->default('Arial');
            $table->string('color_default', 7)->default('#000000');
            $table->string('estado', 50)->default('activo');
            $table->text('notas')->nullable();
            $table->integer('id_us_reg')->default(0);
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
            $table->timestampTz('deleted_at')->nullable();

            $table->index('tipo');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_cert_plantilla');
    }
};
