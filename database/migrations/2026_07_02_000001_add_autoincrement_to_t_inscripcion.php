<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }
        DB::statement('CREATE SEQUENCE IF NOT EXISTS t_inscripcion_id_ins_seq');
        DB::statement('SELECT setval(\'t_inscripcion_id_ins_seq\', COALESCE((SELECT MAX(id_ins) FROM t_inscripcion), 0) + 1, false)');
        DB::statement('ALTER TABLE t_inscripcion ALTER COLUMN id_ins SET DEFAULT nextval(\'t_inscripcion_id_ins_seq\')');
        DB::statement('ALTER SEQUENCE t_inscripcion_id_ins_seq OWNED BY t_inscripcion.id_ins');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE t_inscripcion ALTER COLUMN id_ins DROP DEFAULT');
        DB::statement('DROP SEQUENCE IF EXISTS t_inscripcion_id_ins_seq');
    }
};
