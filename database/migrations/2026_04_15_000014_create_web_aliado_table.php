<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_aliado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 200);
            $table->string('logo_url', 255);
            $table->string('logo_alt', 255)->nullable();
            $table->string('url_sitio', 255)->nullable();
            $table->string('descripcion', 500)->nullable();
            $table->string('tipo', 100)->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_aliado');
    }
};
