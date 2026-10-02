<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campana_lead_id');
            $table->string('nombre', 150);
            $table->string('celular', 30);
            $table->string('correo', 150)->nullable();
            $table->string('profesion', 150)->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
            $table->timestampTz('deleted_at')->nullable();

            $table->foreign('campana_lead_id')->references('id')->on('campanas_leads')->cascadeOnDelete();
            $table->index('campana_lead_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
