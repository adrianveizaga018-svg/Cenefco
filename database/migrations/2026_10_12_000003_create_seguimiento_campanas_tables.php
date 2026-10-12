<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Seguimiento de campañas por marketing:
 *  - registro diario: lo que Facebook no sabe (crecimiento del grupo de WhatsApp, flyers, otros gastos);
 *  - informes: PDF generado en un clic, guardado como historial que no se modifica.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campana_registro_diario', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campana_publicidad_id');
            $table->date('fecha');
            $table->unsignedInteger('nuevos_grupo')->default(0);
            $table->unsignedInteger('total_grupo')->nullable();
            $table->decimal('gasto_extra', 12, 2)->default(0);
            $table->string('flyer_url', 500)->nullable();
            $table->text('nota')->nullable();
            $table->unsignedBigInteger('registrado_por')->nullable();
            $table->timestamps();

            $table->unique(['campana_publicidad_id', 'fecha']);
            $table->foreign('campana_publicidad_id')->references('id')->on('campana_publicidad')->cascadeOnDelete();
        });

        Schema::create('campana_informes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campana_publicidad_id');
            $table->date('desde');
            $table->date('hasta');
            $table->string('archivo_url', 500);
            $table->json('resumen');
            $table->unsignedBigInteger('generado_por')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('campana_publicidad_id');
            $table->foreign('campana_publicidad_id')->references('id')->on('campana_publicidad')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campana_informes');
        Schema::dropIfExists('campana_registro_diario');
    }
};
