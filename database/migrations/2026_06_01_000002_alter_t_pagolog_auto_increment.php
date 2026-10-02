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
            DB::statement('ALTER TABLE t_pagolog DROP CONSTRAINT t_pagolog_pkey');
            DB::statement('CREATE SEQUENCE IF NOT EXISTS t_pagolog_id_pagolog_seq');
            DB::statement("SELECT setval('t_pagolog_id_pagolog_seq', COALESCE((SELECT MAX(id_pagolog) FROM t_pagolog), 0) + 1, false)");
            DB::statement("ALTER TABLE t_pagolog ALTER COLUMN id_pagolog SET DEFAULT nextval('t_pagolog_id_pagolog_seq')");
            DB::statement('ALTER SEQUENCE t_pagolog_id_pagolog_seq OWNED BY t_pagolog.id_pagolog');
            DB::statement('ALTER TABLE t_pagolog ADD PRIMARY KEY (id_pagolog)');
            DB::statement('CREATE INDEX idx_t_pagolog_us_reg ON t_pagolog (id_us_reg)');
        } else {
            DB::statement('ALTER TABLE t_pagolog DROP PRIMARY KEY');
            DB::statement('ALTER TABLE t_pagolog MODIFY id_pagolog INT NOT NULL AUTO_INCREMENT PRIMARY KEY');
            DB::statement('ALTER TABLE t_pagolog ADD INDEX idx_t_pagolog_us_reg (id_us_reg)');
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS idx_t_pagolog_us_reg');
            DB::statement('ALTER TABLE t_pagolog DROP CONSTRAINT t_pagolog_pkey');
            DB::statement('ALTER TABLE t_pagolog ALTER COLUMN id_pagolog DROP DEFAULT');
            DB::statement('DROP SEQUENCE IF EXISTS t_pagolog_id_pagolog_seq');
            DB::statement('ALTER TABLE t_pagolog ADD PRIMARY KEY (id_pagolog, id_us_reg)');
        } else {
            DB::statement('ALTER TABLE t_pagolog DROP PRIMARY KEY');
            DB::statement('ALTER TABLE t_pagolog MODIFY id_pagolog INT NOT NULL');
            DB::statement('ALTER TABLE t_pagolog DROP INDEX idx_t_pagolog_us_reg');
            DB::statement('ALTER TABLE t_pagolog ADD PRIMARY KEY (id_pagolog, id_us_reg)');
        }
    }
};
