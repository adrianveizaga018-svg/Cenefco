<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_pago', function (Blueprint $table) {

            $table->string('estado_verificacion', 20)->default('verificado')->after('comprobante_archivo');
            $table->index('estado_verificacion');
        });
    }

    public function down(): void
    {
        Schema::table('t_pago', function (Blueprint $table) {
            $table->dropIndex(['estado_verificacion']);
            $table->dropColumn('estado_verificacion');
        });
    }
};
