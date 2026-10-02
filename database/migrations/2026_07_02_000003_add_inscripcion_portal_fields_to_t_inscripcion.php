<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_inscripcion', function (Blueprint $table) {
            $table->string('email', 200)->nullable()->after('id_us_reg');
            $table->string('telefono', 30)->nullable()->after('email');
            $table->jsonb('documentos')->nullable()->after('telefono');
            $table->string('origen', 30)->nullable()->default('portal')->after('documentos');
        });
    }

    public function down(): void
    {
        Schema::table('t_inscripcion', function (Blueprint $table) {
            $table->dropColumn(['email', 'telefono', 'documentos', 'origen']);
        });
    }
};
