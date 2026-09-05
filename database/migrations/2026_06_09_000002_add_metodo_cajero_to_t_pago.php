<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_pago', function (Blueprint $table) {
            $table->string('metodo_pago', 40)->nullable()->after('observacion_pago')
                  ->comment('efectivo|deposito_bancario|pago_online|qr');
            $table->unsignedInteger('id_us_cajero')->nullable()->after('metodo_pago')
                  ->comment('FK a t_usuario — quién procesó el cobro presencial');
        });
    }

    public function down(): void
    {
        Schema::table('t_pago', function (Blueprint $table) {
            $table->dropColumn(['metodo_pago', 'id_us_cajero']);
        });
    }
};
