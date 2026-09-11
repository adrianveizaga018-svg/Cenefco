<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('campana_publicidad', function (Blueprint $table) {
            $table->string('cuenta_externa_id', 50)->nullable()->after('id_campana_externa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campana_publicidad', function (Blueprint $table) {
            $table->dropColumn('cuenta_externa_id');
        });
    }
};
