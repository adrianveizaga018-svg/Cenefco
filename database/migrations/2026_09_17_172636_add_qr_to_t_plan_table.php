<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_plan', function (Blueprint $table) {
            $table->string('qr_image_url', 255)->nullable()->after('costo');
        });
    }

    public function down(): void
    {
        Schema::table('t_plan', function (Blueprint $table) {
            $table->dropColumn('qr_image_url');
        });
    }
};
