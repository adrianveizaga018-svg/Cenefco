<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_pago', function (Blueprint $table) {
            $table->text('nota_verificacion')->nullable()->after('estado_verificacion');
        });
    }

    public function down(): void
    {
        Schema::table('t_pago', function (Blueprint $table) {
            $table->dropColumn('nota_verificacion');
        });
    }
};
