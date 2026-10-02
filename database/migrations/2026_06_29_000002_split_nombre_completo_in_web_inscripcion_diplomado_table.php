<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_inscripcion_diplomado', function (Blueprint $table) {
            $table->string('nombre', 200)->nullable()->after('programa_id');
            $table->string('apellido_paterno', 100)->nullable()->after('nombre');
            $table->string('apellido_materno', 100)->nullable()->after('apellido_paterno');
        });

        foreach (DB::table('web_inscripcion_diplomado')->select('id', 'nombre_completo')->get() as $row) {
            $partes = preg_split('/\s+/', trim((string) $row->nombre_completo), -1, PREG_SPLIT_NO_EMPTY);

            DB::table('web_inscripcion_diplomado')->where('id', $row->id)->update([
                'nombre'           => $partes[0] ?? $row->nombre_completo,
                'apellido_paterno' => $partes[1] ?? null,
                'apellido_materno' => implode(' ', array_slice($partes, 2)) ?: null,
            ]);
        }

        Schema::table('web_inscripcion_diplomado', function (Blueprint $table) {
            $table->string('nombre', 200)->nullable(false)->change();
            $table->dropColumn('nombre_completo');
        });
    }

    public function down(): void
    {
        Schema::table('web_inscripcion_diplomado', function (Blueprint $table) {
            $table->string('nombre_completo', 200)->nullable()->after('programa_id');
        });

        foreach (DB::table('web_inscripcion_diplomado')->select('id', 'nombre', 'apellido_paterno', 'apellido_materno')->get() as $row) {
            $completo = trim(implode(' ', array_filter([$row->nombre, $row->apellido_paterno, $row->apellido_materno])));
            DB::table('web_inscripcion_diplomado')->where('id', $row->id)->update(['nombre_completo' => $completo]);
        }

        Schema::table('web_inscripcion_diplomado', function (Blueprint $table) {
            $table->string('nombre_completo', 200)->nullable(false)->change();
            $table->dropColumn(['nombre', 'apellido_paterno', 'apellido_materno']);
        });
    }
};
