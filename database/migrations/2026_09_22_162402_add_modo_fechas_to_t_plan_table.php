<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_plan', function (Blueprint $table) {
            $table->string('modo_fechas', 20)->default('fijo')->after('costo_por_cuota')->comment('fijo o relativo');
        });
    }

    public function down(): void
    {
        Schema::table('t_plan', function (Blueprint $table) {
            $table->dropColumn('modo_fechas');
        });
    }
};
