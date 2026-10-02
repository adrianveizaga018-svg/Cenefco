<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gasto', function (Blueprint $table) {
            $table->unsignedBigInteger('campana_publicidad_id')->nullable()->after('gasto_recurrente_id');
            $table->foreign('campana_publicidad_id')->references('id')->on('campana_publicidad');
            $table->index('campana_publicidad_id');
        });
    }

    public function down(): void
    {
        Schema::table('gasto', function (Blueprint $table) {
            $table->dropForeign(['campana_publicidad_id']);
            $table->dropIndex(['campana_publicidad_id']);
            $table->dropColumn('campana_publicidad_id');
        });
    }
};
