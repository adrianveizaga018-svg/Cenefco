<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('web_programa_etiqueta')) {
            return;
        }
        Schema::create('web_programa_etiqueta', function (Blueprint $table) {
            $table->unsignedInteger('programa_id')
                  ->comment('FK lógica a t_programa');
            $table->foreignId('etiqueta_id')
                  ->constrained('web_etiqueta')
                  ->cascadeOnDelete();
            $table->timestampTz('created_at')->useCurrent();
            $table->primary(['programa_id', 'etiqueta_id']);
        });
    }

    public function down(): void { Schema::dropIfExists('web_programa_etiqueta'); }
};
