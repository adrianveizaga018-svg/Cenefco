<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_pago', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_banco_id')->nullable()->after('comprobante_archivo');
            $table->foreign('tipo_banco_id')->references('id')->on('tipos_banco')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('t_pago', function (Blueprint $table) {
            $table->dropForeign(['tipo_banco_id']);
            $table->dropColumn('tipo_banco_id');
        });
    }
};
