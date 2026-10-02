<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imparticion_planes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_imp');
            $table->unsignedInteger('id_plan');
            $table->timestamps();
            $table->unique(['id_imp', 'id_plan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imparticion_planes');
    }
};
