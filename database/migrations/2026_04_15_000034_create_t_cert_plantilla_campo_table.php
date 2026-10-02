<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_cert_plantilla_campo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('plantilla_id');
            $table->string('clave', 100);
            $table->string('etiqueta', 200);
            $table->string('tipo', 50)->default('texto');
            $table->decimal('pos_x_pct', 6, 3);
            $table->decimal('pos_y_pct', 6, 3);
            $table->decimal('ancho_pct', 6, 3)->nullable();
            $table->decimal('alto_pct', 6, 3)->nullable();
            $table->string('fuente', 100)->nullable();
            $table->integer('tamano_pt')->default(36);
            $table->string('color', 7)->default('#000000');
            $table->string('alineacion', 20)->default('center');
            $table->boolean('negrita')->default(false);
            $table->boolean('cursiva')->default(false);
            $table->string('mayusculas', 20)->default('none');
            $table->string('valor_fijo', 300)->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);

            $table->foreign('plantilla_id')
                ->references('id')
                ->on('t_cert_plantilla')
                ->onDelete('cascade');

            $table->index('plantilla_id');
            $table->unique(['plantilla_id', 'clave']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_cert_plantilla_campo');
    }
};
