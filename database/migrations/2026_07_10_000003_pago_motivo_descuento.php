<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_pago', function (Blueprint $table) {
            $table->text('motivo_descuento')->nullable()->after('monto_descuento_extra');
        });

        Schema::table('t_pagolog', function (Blueprint $table) {
            $table->text('motivo_descuento')->nullable()->after('monto_descuento_extra');
        });
    }

    public function down(): void
    {
        Schema::table('t_pago', function (Blueprint $table) {
            $table->dropColumn('motivo_descuento');
        });

        Schema::table('t_pagolog', function (Blueprint $table) {
            $table->dropColumn('motivo_descuento');
        });
    }
};
