<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_suscriptor', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email', 100)->unique();
            $table->string('nombre', 200)->nullable();
            $table->boolean('confirmado')->default(false);
            $table->string('token_confirmacion', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->string('origen', 100)->nullable();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('ip_origen', 45)->nullable();
            $table->timestampTz('fecha_confirmacion')->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
            $table->timestampTz('deleted_at')->nullable();

            $table->index('confirmado');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_suscriptor');
    }
};
