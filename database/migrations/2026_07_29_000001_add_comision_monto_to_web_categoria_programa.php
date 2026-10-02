<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_categoria_programa', function (Blueprint $table) {
            $table->decimal('comision_monto', 8, 2)->default(0)->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('web_categoria_programa', function (Blueprint $table) {
            $table->dropColumn('comision_monto');
        });
    }
};
