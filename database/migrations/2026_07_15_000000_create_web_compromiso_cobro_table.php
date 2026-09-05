<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_compromiso_cobro', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('id_ins');
            $table->unsignedInteger('id_us');
            $table->unsignedInteger('id_imp');
            $table->date('fecha_compromiso');
            $table->decimal('monto_comprometido', 10, 2)->nullable();
            $table->text('observacion')->nullable();
            $table->string('estado', 20)->default('pendiente');
            $table->unsignedSmallInteger('veces_reprogramado')->default(0);
            $table->unsignedBigInteger('registrado_por');
            $table->timestampTz('notificado_at')->nullable();
            $table->timestampTz('vencido_notificado_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_ins');
            $table->index('fecha_compromiso');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_compromiso_cobro');
    }
};
