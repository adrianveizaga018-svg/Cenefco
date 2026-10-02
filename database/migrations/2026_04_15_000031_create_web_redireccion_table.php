<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_redireccion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url_origen', 500);
            $table->string('url_destino', 500);
            $table->smallInteger('codigo_http')->default(301);
            $table->integer('hits')->default(0);
            $table->boolean('activo')->default(true);
            $table->string('notas', 300)->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->unique('url_origen');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_redireccion');
    }
};
