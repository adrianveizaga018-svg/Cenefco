<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_grado_academico', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 150);        
            $table->string('abreviatura', 30);    
            $table->boolean('requiere_titulo')->default(true); 
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('orden')->default(0);
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->index('activo');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('web_grado_academico');
    }
};
