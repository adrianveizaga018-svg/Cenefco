<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_inscripcion', function (Blueprint $table) {
            $table->unsignedInteger('id_vendedor')->nullable()->after('gestion')
                  ->comment('FK a t_usuario — asesor que realizó la venta');
            $table->string('canal_venta', 30)->nullable()->default('admin')->after('id_vendedor')
                  ->comment('admin|portal|whatsapp|referido');
        });
    }

    public function down(): void
    {
        Schema::table('t_inscripcion', function (Blueprint $table) {
            $table->dropColumn(['id_vendedor', 'canal_venta']);
        });
    }
};
