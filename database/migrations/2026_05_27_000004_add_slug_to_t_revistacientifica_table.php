<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_revistacientifica', function (Blueprint $table) {
            $table->string('slug', 300)->nullable()->unique()->after('titulo_revistacientifica');
        });

        DB::table('t_revistacientifica')->orderBy('id_revistacientifica')->each(function ($row) {
            $base = Str::slug($row->titulo_revistacientifica ?? 'revista-cientifica-' . $row->id_revistacientifica);
            $slug = $base;
            $i = 1;
            while (DB::table('t_revistacientifica')->where('slug', $slug)->where('id_revistacientifica', '!=', $row->id_revistacientifica)->exists()) {
                $slug = $base . '-' . $i++;
            }
            DB::table('t_revistacientifica')->where('id_revistacientifica', $row->id_revistacientifica)->update(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        Schema::table('t_revistacientifica', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
