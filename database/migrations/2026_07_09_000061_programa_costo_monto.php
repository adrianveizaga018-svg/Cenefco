<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_programa', function (Blueprint $table) {

            $table->decimal('costo_monto', 10, 2)->nullable()->after('inversion');
        });
    }

    public function down(): void
    {
        Schema::table('t_programa', function (Blueprint $table) {
            $table->dropColumn('costo_monto');
        });
    }
};
