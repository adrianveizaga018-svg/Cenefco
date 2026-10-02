<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tareas_academicas', function (Blueprint $table) {
            $table->unsignedBigInteger('catalogo_id')->nullable()->after('programa_id');
            $table->foreign('catalogo_id')->references('id')->on('catalogo_tareas_academicas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tareas_academicas', function (Blueprint $table) {
            $table->dropForeign(['catalogo_id']);
            $table->dropColumn('catalogo_id');
        });
    }
};
