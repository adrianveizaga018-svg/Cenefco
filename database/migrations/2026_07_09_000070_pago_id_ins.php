<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_pago', function (Blueprint $table) {

            $table->unsignedBigInteger('id_ins')->nullable()->after('id_fechapago');
            $table->index('id_ins');
        });
    }

    public function down(): void
    {
        Schema::table('t_pago', function (Blueprint $table) {
            $table->dropIndex(['id_ins']);
            $table->dropColumn('id_ins');
        });
    }
};
