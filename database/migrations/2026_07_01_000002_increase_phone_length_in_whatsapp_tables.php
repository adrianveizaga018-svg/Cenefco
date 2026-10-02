<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_conversaciones', function (Blueprint $table) {
            $table->string('phone', 60)->change();
        });

        Schema::table('whatsapp_mensajes', function (Blueprint $table) {
            $table->string('phone', 60)->change();
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_conversaciones', function (Blueprint $table) {
            $table->string('phone', 30)->change();
        });

        Schema::table('whatsapp_mensajes', function (Blueprint $table) {
            $table->string('phone', 30)->change();
        });
    }
};
