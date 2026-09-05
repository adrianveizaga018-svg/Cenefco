<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('envio_certificado', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('id_ins');
            $table->string('ciudad_destino', 150);
            $table->date('fecha_envio');
            $table->string('imagen_guia', 500);
            $table->text('aclaraciones')->nullable();
            $table->decimal('costo', 10, 2)->nullable();
            $table->unsignedInteger('id_us_reg')->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->index('id_ins');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('envio_certificado');
    }
};
