<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_programa', function (Blueprint $table) {
            $table->unsignedBigInteger('area_id')->nullable()->after('categoria_web_id');
            $table->foreign('area_id')->references('id')->on('web_area')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('t_programa', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropColumn('area_id');
        });
    }
};
