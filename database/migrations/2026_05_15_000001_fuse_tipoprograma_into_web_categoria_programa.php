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
        Schema::table('web_categoria_programa', function (Blueprint $table) {
            $table->integer('tipo_programa_id')->nullable()->unique()->after('id');
        });

        $tipos = DB::table('t_tipoprograma')->where('id_us_reg', 0)->get();

        foreach ($tipos as $tipo) {
            $yaExiste = DB::table('web_categoria_programa')
                ->where('tipo_programa_id', $tipo->id_tipoprograma)
                ->exists();

            if ($yaExiste) {
                continue;
            }

            $nombre   = $tipo->nombre_tipoprograma ?? ('Tipo ' . $tipo->id_tipoprograma);
            $baseSlug = Str::slug($nombre) ?: ('tipo-' . $tipo->id_tipoprograma);
            $slug     = $baseSlug;
            $i        = 1;

            while (DB::table('web_categoria_programa')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $i++;
            }

            DB::table('web_categoria_programa')->insert([
                'tipo_programa_id' => $tipo->id_tipoprograma,
                'nombre'           => $nombre,
                'slug'             => $slug,
                'activo'           => (bool) ($tipo->estado ?? 1),
                'orden'            => 0,
                'created_at'       => now()->toDateTimeString(),
                'updated_at'       => now()->toDateTimeString(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('web_categoria_programa', function (Blueprint $table) {
            $table->dropColumn('tipo_programa_id');
        });
    }
};
