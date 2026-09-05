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
        Schema::table('web_docente_perfil', function (Blueprint $table) {
            $table->string('slug', 300)->nullable()->unique()->after('nombre_completo');
        });

        
        $docentes = DB::table('web_docente_perfil')->get(['id', 'nombre_completo']);
        foreach ($docentes as $d) {
            $base = Str::slug($d->nombre_completo);
            $slug = $base ?: 'docente';
            $i = 1;
            while (DB::table('web_docente_perfil')->where('slug', $slug)->where('id', '!=', $d->id)->exists()) {
                $slug = $base . '-' . $i++;
            }
            DB::table('web_docente_perfil')->where('id', $d->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('web_docente_perfil', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
