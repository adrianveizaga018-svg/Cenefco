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
            $table->date('fecha_publicacion')->nullable()->after('fecha_fin')->comment('Fecha real de lanzamiento de la publicación');
            $table->date('fecha_refuerzo')->nullable()->after('fecha_publicacion')->comment('Fecha en que se hizo el refuerzo');
            $table->boolean('en_testeo')->default(false)->after('fecha_refuerzo')->comment('Si la campaña está en modo testeo');
            $table->unsignedInteger('leads_whatsapp')->nullable()->after('en_testeo')->comment('Miembros ingresados al grupo de WhatsApp (manual)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campana_publicidad', function (Blueprint $table) {
            $table->dropColumn(['fecha_publicacion', 'fecha_refuerzo', 'en_testeo', 'leads_whatsapp']);
        });
    }
};
