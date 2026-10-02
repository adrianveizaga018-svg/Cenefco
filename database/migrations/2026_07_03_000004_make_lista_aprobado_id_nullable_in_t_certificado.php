<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_certificado', function (Blueprint $table) {
            $table->dropForeign(['lista_aprobado_id']);
            $table->unsignedBigInteger('lista_aprobado_id')->nullable()->change();
            $table->foreign('lista_aprobado_id')
                ->references('id')
                ->on('t_lista_aprobados')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('t_certificado', function (Blueprint $table) {
            $table->dropForeign(['lista_aprobado_id']);
            $table->unsignedBigInteger('lista_aprobado_id')->nullable(false)->change();
            $table->foreign('lista_aprobado_id')
                ->references('id')
                ->on('t_lista_aprobados')
                ->restrictOnDelete();
        });
    }
};
