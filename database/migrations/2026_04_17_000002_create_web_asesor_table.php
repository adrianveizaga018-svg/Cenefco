<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_asesor', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
            $table->string('telefono', 30)->unique();
            $table->string('email', 100)->nullable();
            $table->string('especialidad', 200)->nullable();
            $table->boolean('disponible')->default(true);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::table('whatsapp_conversaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('asesor_id')->nullable()->after('cliente_id');
            $table->foreign('asesor_id')->references('id')->on('web_asesor')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_conversaciones', function (Blueprint $table) {
            $table->dropForeign(['asesor_id']);
            $table->dropColumn('asesor_id');
        });

        Schema::dropIfExists('web_asesor');
    }
};
