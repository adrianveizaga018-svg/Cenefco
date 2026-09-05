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
        Schema::table('t_tesis', function (Blueprint $table) {
            $table->string('slug', 300)->nullable()->unique()->after('titulo_tesis');
        });

        DB::table('t_tesis')->orderBy('id_tesis')->each(function ($row) {
            $base = Str::slug($row->titulo_tesis ?? 'tesis-' . $row->id_tesis);
            $slug = $base;
            $i = 1;
            while (DB::table('t_tesis')->where('slug', $slug)->where('id_tesis', '!=', $row->id_tesis)->exists()) {
                $slug = $base . '-' . $i++;
            }
            DB::table('t_tesis')->where('id_tesis', $row->id_tesis)->update(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        Schema::table('t_tesis', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
