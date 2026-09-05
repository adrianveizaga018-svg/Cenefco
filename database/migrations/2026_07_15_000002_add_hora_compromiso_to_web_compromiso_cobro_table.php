<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_compromiso_cobro', function (Blueprint $table) {
            $table->time('hora_compromiso')->nullable()->after('fecha_compromiso');
        });
    }

    public function down(): void
    {
        Schema::table('web_compromiso_cobro', function (Blueprint $table) {
            $table->dropColumn('hora_compromiso');
        });
    }
};
