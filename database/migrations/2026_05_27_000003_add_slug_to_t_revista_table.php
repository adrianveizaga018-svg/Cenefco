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
        Schema::table('t_revista', function (Blueprint $table) {
            $table->string('slug', 300)->nullable()->unique()->after('titulo_revista');
        });

        DB::table('t_revista')->orderBy('id_revista')->each(function ($row) {
            $base = Str::slug($row->titulo_revista ?? 'revista-' . $row->id_revista);
            $slug = $base;
            $i = 1;
            while (DB::table('t_revista')->where('slug', $slug)->where('id_revista', '!=', $row->id_revista)->exists()) {
                $slug = $base . '-' . $i++;
            }
            DB::table('t_revista')->where('id_revista', $row->id_revista)->update(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        Schema::table('t_revista', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
