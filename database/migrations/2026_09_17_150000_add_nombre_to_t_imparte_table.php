<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('t_imparte', 'nombre')) {
            Schema::table('t_imparte', function (Blueprint $table) {
                $table->string('nombre', 200)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('t_imparte', 'nombre')) {
            Schema::table('t_imparte', function (Blueprint $table) {
                $table->dropColumn('nombre');
            });
        }
    }
};
