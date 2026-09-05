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
            DB::statement('ALTER TABLE t_pago DROP CONSTRAINT t_pago_pkey');
            DB::statement('CREATE SEQUENCE IF NOT EXISTS t_pago_id_pago_seq');
            DB::statement("SELECT setval('t_pago_id_pago_seq', COALESCE((SELECT MAX(id_pago) FROM t_pago), 0) + 1, false)");
            DB::statement("ALTER TABLE t_pago ALTER COLUMN id_pago SET DEFAULT nextval('t_pago_id_pago_seq')");
            DB::statement('ALTER SEQUENCE t_pago_id_pago_seq OWNED BY t_pago.id_pago');
            DB::statement('ALTER TABLE t_pago ADD PRIMARY KEY (id_pago)');
            DB::statement('CREATE INDEX idx_t_pago_us_reg ON t_pago (id_us_reg)');
        } else {
            DB::statement('ALTER TABLE t_pago DROP PRIMARY KEY');
            DB::statement('ALTER TABLE t_pago MODIFY id_pago INT NOT NULL AUTO_INCREMENT PRIMARY KEY');
            DB::statement('ALTER TABLE t_pago ADD INDEX idx_t_pago_us_reg (id_us_reg)');
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS idx_t_pago_us_reg');
            DB::statement('ALTER TABLE t_pago DROP CONSTRAINT t_pago_pkey');
            DB::statement('ALTER TABLE t_pago ALTER COLUMN id_pago DROP DEFAULT');
            DB::statement('DROP SEQUENCE IF EXISTS t_pago_id_pago_seq');
            DB::statement('ALTER TABLE t_pago ADD PRIMARY KEY (id_pago, id_us_reg)');
        } else {
            DB::statement('ALTER TABLE t_pago DROP PRIMARY KEY');
            DB::statement('ALTER TABLE t_pago MODIFY id_pago INT NOT NULL');
            DB::statement('ALTER TABLE t_pago DROP INDEX idx_t_pago_us_reg');
            DB::statement('ALTER TABLE t_pago ADD PRIMARY KEY (id_pago, id_us_reg)');
        }
    }
};
