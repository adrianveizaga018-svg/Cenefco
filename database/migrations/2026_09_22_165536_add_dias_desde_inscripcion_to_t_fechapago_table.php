<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_fechapago', function (Blueprint $table) {
            $table->integer('dias_desde_inscripcion')->nullable()->after('fecha_fin');
        });
    }

    public function down(): void
    {
        Schema::table('t_fechapago', function (Blueprint $table) {
            $table->dropColumn('dias_desde_inscripcion');
        });
    }
};
