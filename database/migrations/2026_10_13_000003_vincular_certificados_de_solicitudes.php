<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Los certificados generados desde "Solicitudes de certificados" se guardaban sin estudiante
 * (usuario_id = 0) y no aparecían en su perfil. Se vinculan con la inscripción de la solicitud.
 */
return new class extends Migration
{
    public function up(): void
    {
        $filas = DB::table('web_cert_solicitud as s')
            ->join('t_certificado as c', 'c.id', '=', 's.certificado_id')
            ->join('t_inscripcion as i', 'i.id_ins', '=', 's.inscripcion_id')
            ->where('c.usuario_id', 0)
            ->get(['c.id', 'i.id_us', 'i.id_imp']);

        foreach ($filas as $f) {
            DB::table('t_certificado')->where('id', $f->id)->update(['usuario_id' => $f->id_us, 'imparte_id' => $f->id_imp]);
        }
    }

    public function down(): void
    {
        // Sin vuelta atrás: dejar el certificado otra vez sin estudiante no tiene sentido
    }
};
