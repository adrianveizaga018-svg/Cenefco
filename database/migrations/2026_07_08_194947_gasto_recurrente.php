<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gasto_recurrente', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('categoria_gasto_id');
            $table->string('concepto', 200);
            $table->decimal('monto', 10, 2);
            $table->unsignedTinyInteger('dia_del_mes');
            $table->boolean('activo')->default(true);
            $table->date('ultima_confirmacion')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestampTz('deleted_at')->nullable();

            $table->foreign('categoria_gasto_id')->references('id')->on('categoria_gasto');
            $table->index('categoria_gasto_id');
        });

        Schema::table('gasto', function (Blueprint $table) {
            $table->foreign('gasto_recurrente_id')->references('id')->on('gasto_recurrente');
        });
    }

    public function down(): void
    {
        Schema::table('gasto', function (Blueprint $table) {
            $table->dropForeign(['gasto_recurrente_id']);
        });
        Schema::dropIfExists('gasto_recurrente');
    }
};
