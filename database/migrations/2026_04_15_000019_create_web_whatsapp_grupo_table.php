<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_whatsapp_grupo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('imparte_id');
            $table->string('nombre', 200);
            $table->string('enlace_invitacion', 500);
            $table->integer('capacidad_maxima')->nullable();
            $table->integer('miembros_actuales')->default(0);
            $table->string('descripcion', 300)->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestampTz('fecha_expiracion_enlace')->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->index('imparte_id');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_whatsapp_grupo');
    }
};
