<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_acreditacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 300);
            $table->string('entidad_otorgante', 200);
            $table->string('tipo', 100)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('logo_url', 255)->nullable();
            $table->string('logo_alt', 255)->nullable();
            $table->string('url_verificacion', 255)->nullable();
            $table->date('fecha_obtencion')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
            $table->timestampTz('deleted_at')->nullable();

            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_acreditacion');
    }
};
