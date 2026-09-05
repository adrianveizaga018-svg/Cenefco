<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('web_programa_descargable', function (Blueprint $table) {
            $table->unsignedInteger('programa_id')
                  ->comment('FK lógica a t_programa');
            $table->foreignId('descargable_id')
                  ->constrained('web_descargable')
                  ->cascadeOnDelete();
            $table->unsignedTinyInteger('orden')->default(0);
            $table->timestampTz('created_at')->useCurrent();
            $table->primary(['programa_id', 'descargable_id']);
        });
    }

    public function down(): void { Schema::dropIfExists('web_programa_descargable'); }
};
