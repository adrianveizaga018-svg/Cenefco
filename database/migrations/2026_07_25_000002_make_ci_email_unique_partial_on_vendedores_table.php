<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendedores', function (Blueprint $table) {
            $table->dropUnique(['ci']);
            $table->dropUnique(['email']);
        });

        DB::statement('CREATE UNIQUE INDEX vendedores_ci_unique ON vendedores (ci) WHERE deleted_at IS NULL');
        DB::statement('CREATE UNIQUE INDEX vendedores_email_unique ON vendedores (email) WHERE deleted_at IS NULL');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS vendedores_ci_unique');
        DB::statement('DROP INDEX IF EXISTS vendedores_email_unique');

        Schema::table('vendedores', function (Blueprint $table) {
            $table->unique('ci');
            $table->unique('email');
        });
    }
};
