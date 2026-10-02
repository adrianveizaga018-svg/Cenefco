<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            'CREATE UNIQUE INDEX t_inscripcion_activa_unique ON t_inscripcion (id_us, id_imp) WHERE estado = 1'
        );
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS t_inscripcion_activa_unique');
    }
};
