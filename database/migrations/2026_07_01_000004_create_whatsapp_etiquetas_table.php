<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('whatsapp_etiquetas')) {
            Schema::create('whatsapp_etiquetas', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 80);
                $table->string('color', 20)->default('#6366f1');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('whatsapp_conversacion_etiqueta')) {
            Schema::create('whatsapp_conversacion_etiqueta', function (Blueprint $table) {
                $table->foreignId('conversacion_id')->constrained('whatsapp_conversaciones')->cascadeOnDelete();
                $table->foreignId('etiqueta_id')->constrained('whatsapp_etiquetas')->cascadeOnDelete();
                $table->primary(['conversacion_id', 'etiqueta_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversacion_etiqueta');
        Schema::dropIfExists('whatsapp_etiquetas');
    }
};
