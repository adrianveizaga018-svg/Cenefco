<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('config_sitio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 300)->default('CENEFCO');
            $table->string('slogan', 500)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('logo_url', 500)->nullable();
            $table->string('favicon_url', 500)->nullable();
            $table->string('email_contacto', 200)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('direccion', 300)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('pais', 100)->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->string('horario_atencion', 300)->nullable();
            $table->string('meta_titulo', 300)->nullable();
            $table->text('meta_descripcion')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('google_analytics_id', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        DB::table('config_sitio')->insert([
            'nombre'    => 'CENEFCO',
            'slogan'    => 'Centro de Formación Continua',
            'ciudad'    => 'Cochabamba',
            'pais'      => 'Bolivia',
            'activo'    => true,
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('config_sitio');
    }
};
