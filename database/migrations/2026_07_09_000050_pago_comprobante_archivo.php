<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_pago', function (Blueprint $table) {
            $table->string('comprobante_archivo', 500)->nullable()->after('id_us_cajero')
                  ->comment('Ruta del archivo de respaldo (PDF/imagen) subido como comprobante del pago');
        });
    }

    public function down(): void
    {
        Schema::table('t_pago', function (Blueprint $table) {
            $table->dropColumn('comprobante_archivo');
        });
    }
};
