<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipos_banco', function (Blueprint $table) {
            $table->string('numero_cuenta', 100)->nullable()->after('nombre');
            $table->string('titular', 150)->nullable()->after('numero_cuenta');
        });
    }

    public function down(): void
    {
        Schema::table('tipos_banco', function (Blueprint $table) {
            $table->dropColumn(['numero_cuenta', 'titular']);
        });
    }
};
