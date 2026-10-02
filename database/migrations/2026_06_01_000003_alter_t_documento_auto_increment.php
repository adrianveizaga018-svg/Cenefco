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
            DB::statement('ALTER TABLE t_documento DROP CONSTRAINT t_documento_pkey');
            DB::statement('CREATE SEQUENCE IF NOT EXISTS t_documento_id_documento_seq');
            DB::statement("SELECT setval('t_documento_id_documento_seq', COALESCE((SELECT MAX(id_documento) FROM t_documento), 0) + 1, false)");
            DB::statement("ALTER TABLE t_documento ALTER COLUMN id_documento SET DEFAULT nextval('t_documento_id_documento_seq')");
            DB::statement('ALTER SEQUENCE t_documento_id_documento_seq OWNED BY t_documento.id_documento');
            DB::statement('ALTER TABLE t_documento ADD PRIMARY KEY (id_documento)');
            DB::statement('CREATE INDEX idx_t_documento_us_reg ON t_documento (id_us_reg)');
        } else {
            DB::statement('ALTER TABLE t_documento DROP PRIMARY KEY');
            DB::statement('ALTER TABLE t_documento MODIFY id_documento INT NOT NULL AUTO_INCREMENT PRIMARY KEY');
            DB::statement('ALTER TABLE t_documento ADD INDEX idx_t_documento_us_reg (id_us_reg)');
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS idx_t_documento_us_reg');
            DB::statement('ALTER TABLE t_documento DROP CONSTRAINT t_documento_pkey');
            DB::statement('ALTER TABLE t_documento ALTER COLUMN id_documento DROP DEFAULT');
            DB::statement('DROP SEQUENCE IF EXISTS t_documento_id_documento_seq');
            DB::statement('ALTER TABLE t_documento ADD PRIMARY KEY (id_documento, id_us_reg)');
        } else {
            DB::statement('ALTER TABLE t_documento DROP PRIMARY KEY');
            DB::statement('ALTER TABLE t_documento MODIFY id_documento INT NOT NULL');
            DB::statement('ALTER TABLE t_documento DROP INDEX idx_t_documento_us_reg');
            DB::statement('ALTER TABLE t_documento ADD PRIMARY KEY (id_documento, id_us_reg)');
        }
    }
};
