<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_sueldo_docente', function (Blueprint $table) {
            $table->unsignedBigInteger('id_programa')->nullable()->after('id_imp');
            $table->index('id_programa');
        });
    }

    public function down(): void
    {
        Schema::table('web_sueldo_docente', function (Blueprint $table) {
            $table->dropIndex(['id_programa']);
            $table->dropColumn('id_programa');
        });
    }
};
