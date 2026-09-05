<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            'CREATE UNIQUE INDEX t_certificado_lista_aprobado_id_unique ON t_certificado (lista_aprobado_id) WHERE lista_aprobado_id IS NOT NULL'
        );
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS t_certificado_lista_aprobado_id_unique');
    }
};
