<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planilla_detalle', function (Blueprint $table) {

            $table->decimal('monto_base', 10, 2)->nullable()->after('monto');
            $table->decimal('total_descuentos', 10, 2)->default(0)->after('monto_base');
            $table->decimal('total_bonos', 10, 2)->default(0)->after('total_descuentos');
        });
    }

    public function down(): void
    {
        Schema::table('planilla_detalle', function (Blueprint $table) {
            $table->dropColumn(['monto_base', 'total_descuentos', 'total_bonos']);
        });
    }
};
