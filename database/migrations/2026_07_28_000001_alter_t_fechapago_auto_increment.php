<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE t_fechapago DROP CONSTRAINT t_fechapago_pkey');
            DB::statement('CREATE SEQUENCE IF NOT EXISTS t_fechapago_id_fechapago_seq');
            DB::statement("SELECT setval('t_fechapago_id_fechapago_seq', COALESCE((SELECT MAX(id_fechapago) FROM t_fechapago), 0) + 1, false)");
            DB::statement("ALTER TABLE t_fechapago ALTER COLUMN id_fechapago SET DEFAULT nextval('t_fechapago_id_fechapago_seq')");
            DB::statement('ALTER SEQUENCE t_fechapago_id_fechapago_seq OWNED BY t_fechapago.id_fechapago');
            DB::statement('ALTER TABLE t_fechapago ADD PRIMARY KEY (id_fechapago)');
            DB::statement('CREATE INDEX idx_t_fechapago_us_reg ON t_fechapago (id_us_reg)');
        } else {
            DB::statement('ALTER TABLE t_fechapago DROP PRIMARY KEY');
            DB::statement('ALTER TABLE t_fechapago MODIFY id_fechapago INT NOT NULL AUTO_INCREMENT PRIMARY KEY');
            DB::statement('ALTER TABLE t_fechapago ADD INDEX idx_t_fechapago_us_reg (id_us_reg)');
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS idx_t_fechapago_us_reg');
            DB::statement('ALTER TABLE t_fechapago DROP CONSTRAINT t_fechapago_pkey');
            DB::statement('ALTER TABLE t_fechapago ALTER COLUMN id_fechapago DROP DEFAULT');
            DB::statement('DROP SEQUENCE IF EXISTS t_fechapago_id_fechapago_seq');
            DB::statement('ALTER TABLE t_fechapago ADD PRIMARY KEY (id_fechapago, id_plan, id_us_reg)');
        } else {
            DB::statement('ALTER TABLE t_fechapago DROP PRIMARY KEY');
            DB::statement('ALTER TABLE t_fechapago MODIFY id_fechapago INT NOT NULL');
            DB::statement('ALTER TABLE t_fechapago DROP INDEX idx_t_fechapago_us_reg');
            DB::statement('ALTER TABLE t_fechapago ADD PRIMARY KEY (id_fechapago, id_plan, id_us_reg)');
        }
    }
};
