<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('config_sitio', function (Blueprint $table) {
            $table->string('whatsapp_numero', 30)->nullable()->after('telefono');
            $table->string('whatsapp_mensaje', 300)->nullable()->after('whatsapp_numero');
        });
    }

    public function down(): void
    {
        Schema::table('config_sitio', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_numero', 'whatsapp_mensaje']);
        });
    }
};
