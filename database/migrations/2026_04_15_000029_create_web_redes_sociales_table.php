<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_redes_sociales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('red', 50);
            $table->string('nombre_display', 100)->nullable();
            $table->string('url', 255)->nullable();
            $table->string('icono_clase', 100)->nullable();
            $table->string('pixel_id', 100)->nullable();
            $table->boolean('mostrar_footer')->default(true);
            $table->boolean('mostrar_header')->default(false);
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestampTz('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_redes_sociales');
    }
};
