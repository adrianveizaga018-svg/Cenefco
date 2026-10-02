<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('estado', 30)->default('nuevo')->after('profesion'); // nuevo/contactado/interesado/inscrito/descartado
            $table->unsignedBigInteger('vendedor_asignado_id')->nullable()->after('estado');
            $table->unsignedBigInteger('programa_id')->nullable()->after('vendedor_asignado_id');
            $table->index('estado');
            $table->index('vendedor_asignado_id');
        });
    }
    public function down(): void {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['estado', 'vendedor_asignado_id', 'programa_id']);
        });
    }
};
