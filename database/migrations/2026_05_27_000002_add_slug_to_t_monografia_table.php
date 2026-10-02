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
        Schema::table('t_monografia', function (Blueprint $table) {
            $table->string('slug', 300)->nullable()->unique()->after('titulo_monografia');
        });

        DB::table('t_monografia')->orderBy('id_monografia')->each(function ($row) {
            $base = Str::slug($row->titulo_monografia ?? 'monografia-' . $row->id_monografia);
            $slug = $base;
            $i = 1;
            while (DB::table('t_monografia')->where('slug', $slug)->where('id_monografia', '!=', $row->id_monografia)->exists()) {
                $slug = $base . '-' . $i++;
            }
            DB::table('t_monografia')->where('id_monografia', $row->id_monografia)->update(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        Schema::table('t_monografia', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
