<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $duplicados = DB::table('t_inscripcion')
            ->select('id_ins')
            ->groupBy('id_ins')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('id_ins');

        if ($duplicados->isNotEmpty()) {
            throw new \RuntimeException(
                'No se puede aplicar el índice único: existen id_ins duplicados en t_inscripcion: '
                . $duplicados->implode(', ')
                . '. Resuelva estos duplicados (revisar id_us_reg de cada fila) antes de reintentar esta migración.'
            );
        }

        DB::statement('CREATE UNIQUE INDEX t_inscripcion_id_ins_unique ON t_inscripcion (id_ins)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS t_inscripcion_id_ins_unique');
    }
};
