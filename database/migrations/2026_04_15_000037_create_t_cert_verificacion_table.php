<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_cert_verificacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('certificado_id')->nullable();
            $table->string('codigo_consultado', 100);
            $table->string('resultado', 20);
            $table->string('ip_origen', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('pais', 100)->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();

            $table->foreign('certificado_id')
                ->references('id')
                ->on('t_certificado')
                ->onDelete('set null');

            $table->index('certificado_id');
            $table->index('codigo_consultado');
            $table->index('ip_origen');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_cert_verificacion');
    }
};
