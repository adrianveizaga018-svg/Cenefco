<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lead_seguimientos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('vendedor_id')->nullable(); // usuario que hizo el seguimiento
            $table->string('tipo', 30); // llamada / whatsapp / correo / reunion / otro
            $table->string('resultado', 50); // no_contesto / contactado / interesado / no_interesado / inscrito
            $table->text('nota')->nullable();
            $table->string('proxima_accion', 200)->nullable();
            $table->date('fecha_proxima_accion')->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
            $table->foreign('lead_id')->references('id')->on('leads')->cascadeOnDelete();
            $table->index('lead_id');
            $table->index('vendedor_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('lead_seguimientos');
    }
};
