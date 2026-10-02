<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_ia_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 60)->index();
            $table->text('input');
            $table->text('prompt')->nullable();
            $table->text('output')->nullable();
            $table->string('modelo', 100)->nullable();
            $table->unsignedInteger('tokens_in')->nullable();
            $table->unsignedInteger('tokens_out')->nullable();
            $table->unsignedInteger('latencia_ms')->nullable();
            $table->boolean('error')->default(false);
            $table->timestampTz('created_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_ia_logs');
    }
};
