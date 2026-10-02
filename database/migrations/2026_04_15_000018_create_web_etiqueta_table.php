<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_etiqueta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->string('color', 7)->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
        });

        Schema::create('web_articulo_etiqueta', function (Blueprint $table) {
            $table->unsignedInteger('articulo_id');
            $table->unsignedBigInteger('etiqueta_id');
            $table->foreign('etiqueta_id')
                ->references('id')
                ->on('web_etiqueta')
                ->onDelete('cascade');

            $table->primary(['articulo_id', 'etiqueta_id']);
        });

        Schema::create('web_programa_etiqueta', function (Blueprint $table) {
            $table->unsignedInteger('programa_id');
            $table->unsignedBigInteger('etiqueta_id');
            $table->foreign('etiqueta_id')
                ->references('id')
                ->on('web_etiqueta')
                ->onDelete('cascade');

            $table->primary(['programa_id', 'etiqueta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_programa_etiqueta');
        Schema::dropIfExists('web_articulo_etiqueta');
        Schema::dropIfExists('web_etiqueta');
    }
};
