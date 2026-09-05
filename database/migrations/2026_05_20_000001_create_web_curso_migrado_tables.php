<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_curso_migrado', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 300);
            $table->string('slug', 300)->unique();
            $table->string('url', 500);
            $table->string('qr_path', 500)->nullable();
            $table->string('periodo', 50)->nullable();
            $table->string('gestion', 10)->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->timestampsTz();
        });

        Schema::create('web_participante_migrado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('web_curso_migrado')->cascadeOnDelete();
            $table->string('nombre_completo', 300);
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_participante_migrado');
        Schema::dropIfExists('web_curso_migrado');
    }
};
