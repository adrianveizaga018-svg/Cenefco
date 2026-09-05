<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gasto', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('categoria_gasto_id');
            $table->string('concepto', 200);
            $table->decimal('monto', 10, 2);
            $table->date('fecha');
            $table->string('responsable', 150)->nullable();
            $table->string('comprobante_url', 500)->nullable();
            $table->text('nota')->nullable();
            $table->unsignedBigInteger('gasto_recurrente_id')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestampTz('deleted_at')->nullable();

            $table->foreign('categoria_gasto_id')->references('id')->on('categoria_gasto');
            $table->index('categoria_gasto_id');
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gasto');
    }
};
