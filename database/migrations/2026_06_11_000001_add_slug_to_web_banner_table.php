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
        Schema::table('web_banner', function (Blueprint $table) {
            $table->string('slug', 300)->nullable()->unique()->after('titulo');
        });

        $banners = DB::table('web_banner')->orderBy('id')->get(['id', 'titulo']);
        foreach ($banners as $b) {
            $base = Str::slug($b->titulo ?? '') ?: 'banner';
            $slug = $base;
            $i    = 1;
            while (DB::table('web_banner')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }
            DB::table('web_banner')->where('id', $b->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('web_banner', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
