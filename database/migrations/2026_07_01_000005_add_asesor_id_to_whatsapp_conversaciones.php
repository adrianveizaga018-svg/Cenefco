<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_conversaciones', function (Blueprint $table) {
            if (! Schema::hasColumn('whatsapp_conversaciones', 'asesor_id')) {
                $table->unsignedBigInteger('asesor_id')->nullable()->after('cliente_id');
            }
            if (\DB::connection()->getDriverName() === 'pgsql' && ! collect(\DB::select("SELECT indexname FROM pg_indexes WHERE tablename='whatsapp_conversaciones' AND indexname='idx_conv_asesor_id'"))->count()) {
                $table->index('asesor_id', 'idx_conv_asesor_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_conversaciones', function (Blueprint $table) {
            $table->dropIndex('idx_conv_asesor_id');
            $table->dropColumn('asesor_id');
        });
    }
};
