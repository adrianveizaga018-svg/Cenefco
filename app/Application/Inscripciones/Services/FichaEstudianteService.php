<?php

namespace App\Application\Inscripciones\Services;

use App\Application\Inscripciones\Queries\GetInscripcionDetalleQuery;
use App\Application\Inscripciones\QueryHandlers\GetInscripcionDetalleQueryHandler;
use Illuminate\Support\Facades\DB;

/**
 * Vista completa de un estudiante para el personal (Caja / académico):
 * sus datos, su acceso a la plataforma, todos sus programas con el estado
 * de pago y todos sus certificados.
 */
class FichaEstudianteService
{
    public function __construct(
        private readonly GetInscripcionDetalleQueryHandler $detalleHandler,
    ) {}

    /** Busca por carnet (exacto o parcial) o por nombre/apellido. */
    public function buscar(string $texto): array
    {
        $texto = trim($texto);
        if (mb_strlen($texto) < 3) {
            return [];
        }

        $like = '%'.mb_strtolower($texto, 'UTF-8').'%';

        return DB::table('t_usuario')
            ->where(fn ($q) => $q
                ->where('ci', $texto)
                ->orWhereRaw('LOWER(ci) LIKE ?', [$like])
                ->orWhereRaw('LOWER(nombre) LIKE ?', [$like])
                ->orWhereRaw('LOWER(appaterno) LIKE ?', [$like])
                ->orWhereRaw('LOWER(apmaterno) LIKE ?', [$like]))
            ->whereNotNull('ci')
            ->orderByRaw('CASE WHEN ci = ? THEN 0 ELSE 1 END', [$texto])
            ->orderBy('appaterno')
            ->limit(20)
            ->get(['id_us', 'nombre', 'appaterno', 'apmaterno', 'ci', 'celular', 'email'])
            ->unique('id_us')
            ->values()
            ->all();
    }

    public function ficha(int $idUs): ?array
    {
        $estudiante = DB::table('t_usuario')
            ->where('id_us', $idUs)
            ->orderBy('id_us_reg')
            ->first(['id_us', 'nombre', 'appaterno', 'apmaterno', 'ci', 'expedido', 'celular', 'email', 'genero', 'fecha_reg']);

        if (! $estudiante) {
            return null;
        }

        return [
            'estudiante'    => $estudiante,
            'cuenta'        => $this->cuenta($estudiante),
            'inscripciones' => $this->inscripciones($idUs),
            'certificados'  => $this->certificados($idUs),
        ];
    }

    /** Cuenta de la plataforma enlazada (por carnet o, si no, por correo). */
    private function cuenta(object $estudiante): ?array
    {
        $cuenta = DB::table('usuarios as u')
            ->leftJoin('usuarios_roles as ur', 'ur.usuario_id', '=', 'u.id')
            ->leftJoin('roles as r', 'r.id', '=', 'ur.rol_id')
            ->whereNull('u.deleted_at')
            ->where(fn ($q) => $q
                ->when($estudiante->ci, fn ($w) => $w->orWhere('u.ci', $estudiante->ci))
                ->when($estudiante->email, fn ($w) => $w->orWhere('u.email', mb_strtolower($estudiante->email))))
            ->orderByRaw('CASE WHEN u.ci = ? THEN 0 ELSE 1 END', [(string) $estudiante->ci])
            ->first(['u.id', 'u.email', 'u.activo', 'u.email_verificado', 'u.google_id', 'r.nombre as rol', 'u.created_at']);

        if (! $cuenta) {
            return null;
        }

        return [
            'id'      => $cuenta->id,
            'email'   => $cuenta->email,
            'activo'  => (bool) $cuenta->activo,
            // Correo verificado = ya creó su contraseña con el enlace o entró con Google
            'activada' => (bool) $cuenta->email_verificado || ! empty($cuenta->google_id),
            'rol'     => $cuenta->rol,
            'creada'  => $cuenta->created_at,
        ];
    }

    private function inscripciones(int $idUs): array
    {
        $filas = DB::table('t_inscripcion as ins')
            ->join('t_imparte as imp', 'imp.id_imp', '=', 'ins.id_imp')
            ->leftJoin('t_programa as prog', 'prog.id_programa', '=', 'imp.id_mat')
            ->where('ins.id_us', $idUs)
            ->orderByDesc('ins.fecha_ins')
            ->orderByDesc('ins.id_ins')
            ->get([
                'ins.id_ins', 'ins.id_imp', 'ins.fecha_ins', 'ins.estado', 'ins.canal_venta',
                'prog.id_programa', 'prog.nombre_programa', 'imp.nombre as version', 'imp.gestion',
            ])
            ->unique('id_ins');

        return $filas->map(function ($f) {
            $resumen = $this->detalleHandler->handle(new GetInscripcionDetalleQuery((int) $f->id_ins))->resumen;

            return [
                'id_ins'           => $f->id_ins,
                'id_imp'           => $f->id_imp,
                'id_programa'      => $f->id_programa,
                'programa'         => $f->nombre_programa ?? 'Programa sin nombre',
                'version'          => $f->version,
                'gestion'          => $f->gestion,
                'fecha_ins'        => $f->fecha_ins,
                'activa'           => (int) $f->estado === 1,
                'canal'            => $f->canal_venta,
                'total_pagado'     => $resumen->total_pagado,
                'pendiente'        => $resumen->pendiente,
                'plan_no_asignado' => $resumen->plan_no_asignado,
                'pago_completo'    => $resumen->pendiente !== null && $resumen->pendiente <= 0,
            ];
        })->values()->all();
    }

    private function certificados(int $idUs): array
    {
        return DB::table('t_certificado as c')
            ->leftJoin('t_imparte as imp', 'imp.id_imp', '=', 'c.imparte_id')
            ->leftJoin('t_programa as prog', 'prog.id_programa', '=', 'imp.id_mat')
            ->where('c.usuario_id', $idUs)
            ->orderByDesc('c.created_at')
            ->get([
                'c.id', 'c.imparte_id', 'c.codigo_verificacion', 'c.archivo_url', 'c.estado', 'c.condicion', 'c.created_at',
                DB::raw('COALESCE(c.programa_en_certificado, prog.nombre_programa) as programa'),
            ])
            ->unique('id')
            ->values()
            ->all();
    }
}
