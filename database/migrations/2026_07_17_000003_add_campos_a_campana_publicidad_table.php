<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campana_publicidad', function (Blueprint $table) {
            $table->integer('leads')->nullable()->after('estado');
            $table->decimal('presupuesto_usd', 10, 2)->nullable()->after('leads');
            $table->decimal('presupuesto_bob', 10, 2)->nullable()->after('presupuesto_usd');
        });

        Schema::table('campana_publicidad', function (Blueprint $table) {
            $table->dropColumn(['presupuesto_planificado', 'moneda']);
        });
    }

    public function down(): void
    {
        Schema::table('campana_publicidad', function (Blueprint $table) {
            $table->decimal('presupuesto_planificado', 10, 2)->nullable();
            $table->string('moneda', 10)->default('BOB');
        });

        Schema::table('campana_publicidad', function (Blueprint $table) {
            $table->dropColumn(['leads', 'presupuesto_usd', 'presupuesto_bob']);
        });
    }
};
