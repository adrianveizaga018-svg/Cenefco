<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\DB;

trait DisablesForeignKeys
{
    protected function disableForeignKeys(): void
    {
        match (DB::connection()->getDriverName()) {
            'pgsql'   => DB::statement("SET session_replication_role = 'replica'"),
            'sqlite'  => DB::statement('PRAGMA foreign_keys = OFF'),
            default   => DB::statement('SET FOREIGN_KEY_CHECKS=0'),
        };
    }

    protected function enableForeignKeys(): void
    {
        match (DB::connection()->getDriverName()) {
            'pgsql'   => DB::statement("SET session_replication_role = 'origin'"),
            'sqlite'  => DB::statement('PRAGMA foreign_keys = ON'),
            default   => DB::statement('SET FOREIGN_KEY_CHECKS=1'),
        };
    }
}
