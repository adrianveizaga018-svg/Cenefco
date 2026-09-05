<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_curso_migrado', function (Blueprint $table) {
            $table->string('imagen_path', 500)->nullable()->after('qr_path');
            $table->unsignedSmallInteger('carga_horaria')->nullable()->after('fecha_inicio');
        });
    }

    public function down(): void
    {
        Schema::table('web_curso_migrado', function (Blueprint $table) {
            $table->dropColumn(['imagen_path', 'carga_horaria']);
        });
    }
};
