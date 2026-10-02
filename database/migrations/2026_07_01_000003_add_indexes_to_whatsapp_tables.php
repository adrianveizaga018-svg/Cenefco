<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_mensajes', function (Blueprint $table) {
            $table->index(['conversacion_id', 'created_at'], 'idx_mensajes_conv_date');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_mensajes', function (Blueprint $table) {
            $table->dropIndex('idx_mensajes_conv_date');
        });
    }
};
