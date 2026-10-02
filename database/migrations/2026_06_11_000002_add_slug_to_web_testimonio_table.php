<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_testimonio', function (Blueprint $table) {
            $table->string('slug', 300)->nullable()->unique()->after('nombre');
        });

        $rows = DB::table('web_testimonio')->orderBy('id')->get(['id', 'nombre']);
        foreach ($rows as $row) {
            $base = Str::slug($row->nombre ?? '') ?: 'testimonio';
            $slug = $base;
            $i    = 1;
            while (DB::table('web_testimonio')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }
            DB::table('web_testimonio')->where('id', $row->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('web_testimonio', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
