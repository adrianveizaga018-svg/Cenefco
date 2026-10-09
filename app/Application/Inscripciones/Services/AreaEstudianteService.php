<?php

namespace App\Application\Inscripciones\Services;

use App\Application\Usuarios\Services\PromocionEstudianteService;
use App\Domain\Inscripciones\Contracts\InscripcionRepositoryInterface;
use App\Infrastructure\Usuarios\Models\User;
use App\Shared\Kernel\Support\PlanGratuito;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Datos del panel del estudiante: sus cursos, pagos, certificados y el catálogo
 * de programas que todavía puede tomar. La cuenta (usuarios) se enlaza con el
 * registro académico (t_usuario) por CI y, si falta, por correo.
 */
class AreaEstudianteService
{
    public function __construct(
        private readonly InscripcionRepositoryInterface $inscripciones,
        private readonly PromocionEstudianteService $promocion,
        private readonly InscripcionCuotasService $cuotasService,
    ) {}

    public function idUs(User $user): ?int
    {
        if (! empty($user->ci)) {
            $id = DB::table('t_usuario')->where('ci', $user->ci)->min('id_us');
            if ($id) {
                return (int) $id;
            }
        }
        if (! empty($user->email)) {
            $id = DB::table('t_usuario')->whereRaw('LOWER(email) = ?', [mb_strtolower($user->email)])->min('id_us');
            if ($id) {
                return (int) $id;
            }
        }

        return null;
    }

    /** Programas en los que está inscrito (inscripción activa). */
    public function cursos(?int $idUs): Collection
    {
        if (! $idUs) {
            return collect();
        }

        $hoy = now()->toDateString();

        $cursos = DB::table('t_inscripcion as ins')
            ->leftJoin('t_imparte as imp', function ($j) {
                $j->on('ins.id_imp', '=', 'imp.id_imp')
                  ->whereRaw('imp.id_us_reg = (SELECT MIN(i2.id_us_reg) FROM t_imparte i2 WHERE i2.id_imp = imp.id_imp)');
            })
            ->leftJoin('t_programa as prog', 'prog.id_programa', '=', 'imp.id_mat')
            ->leftJoin('t_materia as m', function ($j) {
                $j->on('m.id_mat', '=', 'imp.id_mat')
                  ->whereRaw('m.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = m.id_mat)');
            })
            ->leftJoin('t_usuario as doc', function ($j) {
                $j->on('doc.id_us', '=', 'imp.id_us')
                  ->whereRaw('doc.id_us_reg = (SELECT MIN(d2.id_us_reg) FROM t_usuario d2 WHERE d2.id_us = doc.id_us)');
            })
            ->where('ins.id_us', $idUs)
            ->where('ins.estado', 1)
            ->select([
                'ins.id_ins', 'ins.fecha_ins', 'ins.estado as estado_inscripcion', 'ins.id_plan',
                'imp.id_imp', 'imp.titulo_personalizado', 'imp.imparte_fecha_inicio', 'imp.imparte_fecha_fin',
                'imp.whatsapp_grupo_url', 'imp.whatsapp_grupo_activo',
                'prog.slug', 'prog.foto',
                DB::raw('COALESCE(prog.nombre_programa, imp.titulo_personalizado, m.nombre, m.nombremat) as nombre_materia'),
                'm.carga_horaria as horas_academicas',
                DB::raw(\App\Shared\Kernel\Support\SqlCompat::trimConcat("COALESCE(doc.nombre,'')", "' '", "COALESCE(doc.appaterno,'')") . ' as nombre_docente'),
            ])
            ->orderByDesc('ins.fecha_ins')
            ->get();

        $conCertificado = DB::table('t_certificado')
            ->where('usuario_id', $idUs)
            ->where('estado', '!=', 'anulado')
            ->pluck('imparte_id')
            ->map(fn ($v) => (int) $v)
            ->all();

        $hayGrabaciones = Schema::hasTable('t_zoom_grabacion') && Schema::hasTable('t_zoom_reunion');

        return $cursos->map(function ($c) use ($hoy, $conCertificado, $hayGrabaciones) {
            $inicio = $c->imparte_fecha_inicio;
            $fin    = $c->imparte_fecha_fin;

            $c->estado_curso = match (true) {
                $fin && $fin < $hoy       => 'finalizado',
                $inicio && $inicio > $hoy => 'por_iniciar',
                default                   => 'en_curso',
            };
            $c->nombre_docente   = trim((string) $c->nombre_docente) ?: null;
            $c->foto             = $this->urlArchivo($c->foto);
            $c->whatsapp_grupo_url = $c->whatsapp_grupo_activo ? $c->whatsapp_grupo_url : null;
            $c->tiene_certificado = in_array((int) $c->id_imp, $conCertificado, true);
            $c->gratuito         = PlanGratuito::es($c->id_plan ? (int) $c->id_plan : null);
            $c->grabaciones      = $hayGrabaciones
                ? DB::table('t_zoom_grabacion as g')
                    ->join('t_zoom_reunion as r', 'g.reunion_id', '=', 'r.id')
                    ->where('r.imparte_id', $c->id_imp)
                    ->orderByDesc('g.recording_start')
                    ->get(['g.id', 'r.tema', 'g.play_url', 'g.duracion', 'g.recording_start'])
                : [];
            unset($c->whatsapp_grupo_activo);

            return $c;
        })->values();
    }

    /**
     * Estado de pago por inscripción, desde el mismo cronograma que usan Caja,
     * Cobranzas y la ficha (InscripcionCuotasService). Sin cronograma: el cálculo del detalle.
     */
    public function pagos(?int $idUs): array
    {
        if (! $idUs) {
            return [];
        }

        $hoy = now()->toDateString();

        return $this->cursos($idUs)->map(function ($c) use ($hoy) {
            $idIns = (int) $c->id_ins;

            $pagos = DB::table('t_pago')
                ->where('id_ins', $idIns)
                ->where('estado', 1)
                ->orderBy('fecha_deposito')
                ->get(['id_pago', 'monto_pagado', 'fecha_deposito', 'metodo_pago', 'estado_verificacion', 'nota_verificacion']);

            $cronograma = $this->cuotasService->sincronizar($idIns);

            if ($cronograma) {
                $porId  = $pagos->keyBy('id_pago');
                $cuotas = collect($cronograma->cuotas)->map(function ($q) use ($porId, $hoy) {
                    $pagada = $q->estado === 'pagado';
                    $pago   = $q->id_pago ? $porId->get($q->id_pago) : null;
                    $saldo  = round((float) $q->monto_a_pagar - (float) $q->monto_pagado, 2);

                    return [
                        'id_fechapago'  => $q->id,
                        'nro_pago'      => (int) $q->nro_cuota,
                        'descripcion'   => $q->descripcion,
                        'monto_a_pagar' => (float) $q->monto_a_pagar,
                        'saldo'         => max(0.0, $saldo),
                        'estado'        => $q->estado,
                        'fecha_limite'  => $q->fecha_vencimiento,
                        'pagado'        => $pagada,
                        'vencida'       => ! $pagada && $q->fecha_vencimiento && $q->fecha_vencimiento < $hoy,
                        'monto_pagado'  => (float) $q->monto_pagado,
                        'fecha_pago'    => $pago->fecha_deposito ?? null,
                        'metodo_pago'   => $pago->metodo_pago ?? null,
                    ];
                })->values();
                $totalPlan   = (float) $cronograma->total_plan;
                $saldo       = (float) $cronograma->pendiente;
                $totalPagado = (float) $cronograma->total_pagado;
            } else {
                $resumen     = $this->inscripciones->findDetalleById($idIns)->resumen;
                $totalPlan   = (float) ($resumen->total_plan ?? 0);
                $saldo       = (float) ($resumen->pendiente ?? 0);
                $totalPagado = (float) ($resumen->total_pagado ?? 0);
                $cuotas      = collect();
            }

            $enRevision = $pagos->where('estado_verificacion', InscripcionCuotasService::COMPROBANTE_ESTUDIANTE)
                ->map(fn ($p) => ['id_pago' => $p->id_pago, 'monto' => (float) $p->monto_pagado, 'fecha' => $p->fecha_deposito])
                ->values()->all();

            return [
                'id_ins'          => $idIns,
                'nombre_programa' => $c->nombre_materia,
                'programa_slug'   => $c->slug,
                'fecha_ins'       => $c->fecha_ins,
                'fecha_inicio'    => $c->imparte_fecha_inicio,
                'fecha_fin'       => $c->imparte_fecha_fin,
                'gratuito'        => $c->gratuito,
                'requiere_plan'   => ! $c->id_plan, // preinscripción del portal sin plan elegido
                'total_plan'      => $totalPlan,
                'total_pagado'    => $totalPagado,
                'saldo'           => $saldo,
                'estado_pago'     => match (true) {
                    $c->gratuito                  => 'gratuito',
                    $saldo <= 0 && $totalPlan > 0 => 'pagado',
                    $totalPagado > 0              => 'parcial',
                    default                       => 'pendiente',
                },
                'cuotas'          => $cuotas->all(),
                'comprobantes_en_revision' => $enRevision,
                'nro_pagos'       => $pagos->count(),
            ];
        })->values()->all();
    }

    public function certificados(?int $idUs): Collection
    {
        if (! $idUs) {
            return collect();
        }

        return DB::table('t_certificado')
            ->where('usuario_id', $idUs)
            ->where('estado', '!=', 'anulado')
            ->orderByDesc('created_at')
            ->get(['id', 'nombre_en_certificado', 'programa_en_certificado', 'codigo_verificacion', 'qr_url', 'archivo_url', 'estado', 'created_at'])
            ->map(function ($c) {
                $c->archivo_url = $this->urlArchivo($c->archivo_url);
                $c->qr_url      = $this->urlArchivo($c->qr_url);

                return $c;
            });
    }

    /** Programas publicados con una versión abierta en los que el estudiante aún no está inscrito. */
    public function catalogo(?int $idUs): array
    {
        $hoy = now()->toDateString();

        $versiones = DB::table('t_programa as prog')
            ->join('t_imparte as imp', 'imp.id_mat', '=', 'prog.id_programa')
            ->where('prog.estado_web', 'publicado')
            ->where('prog.estado', 1)
            ->whereNull('prog.deleted_at')
            ->where('imp.estado', 1)
            ->where(fn ($q) => $q->whereNull('imp.imparte_fecha_fin')->orWhere('imp.imparte_fecha_fin', '>=', $hoy))
            ->orderBy('imp.imparte_fecha_inicio')
            ->get([
                'prog.id_programa', 'prog.nombre_programa', 'prog.slug', 'prog.foto', 'prog.descripcion',
                'prog.creditaje', 'prog.formulario_id', 'imp.id_imp', 'imp.imparte_fecha_inicio', 'imp.imparte_fecha_fin',
            ])
            ->unique('id_programa');

        $yaInscrito = $idUs
            ? DB::table('t_inscripcion as ins')
                ->join('t_imparte as imp', 'imp.id_imp', '=', 'ins.id_imp')
                ->where('ins.id_us', $idUs)
                ->where('ins.estado', 1)
                ->pluck('imp.id_mat')->map(fn ($v) => (int) $v)->all()
            : [];

        $portal = rtrim((string) config('services.portal.url'), '/');

        return $versiones
            ->reject(fn ($v) => in_array((int) $v->id_programa, $yaInscrito, true))
            ->map(function ($v) use ($portal) {
                $planes = $this->planesDe((int) $v->id_imp, (int) $v->id_programa);
                $costos = $planes->where('gratuito', false)->pluck('costo')->filter(fn ($c) => $c > 0);

                return [
                    'id_programa'  => (int) $v->id_programa,
                    'id_imp'       => (int) $v->id_imp,
                    'nombre'       => $v->nombre_programa,
                    'slug'         => $v->slug,
                    'foto'         => $this->urlArchivo($v->foto),
                    'resumen'      => Str::limit(trim(html_entity_decode(strip_tags((string) $v->descripcion))), 160),
                    'creditaje'    => $v->creditaje,
                    'fecha_inicio' => $v->imparte_fecha_inicio,
                    'fecha_fin'    => $v->imparte_fecha_fin,
                    'planes'       => $planes->values()->all(),
                    'gratuito'     => $planes->contains('gratuito', true),
                    'precio_desde' => $costos->isNotEmpty() ? (float) $costos->min() : null,
                    'url_portal'   => $v->slug ? "{$portal}/cursos/{$v->slug}" : null,
                    // Con formulario propio (datos y documentos) la inscripción se hace en el portal
                    'requiere_formulario' => (bool) $v->formulario_id,
                ];
            })
            ->sortByDesc('gratuito')
            ->values()
            ->all();
    }

    /**
     * El estudiante se inscribe solo en un programa con plan gratuito.
     * Si aún no tiene registro académico, se crea con los datos de su cuenta.
     */
    public function inscribirGratis(User $user, int $idImp, int $idPlan): array
    {
        [$imp, $plan] = $this->validarOferta($user, $idImp, $idPlan);
        if (! $plan['gratuito'] || ! PlanGratuito::es($idPlan)) {
            throw new DomainException('Este plan no es gratuito. Para inscribirte elige "Inscribirme" y sube tu comprobante de pago.');
        }

        $idIns = DB::transaction(fn () => $this->crearInscripcion($user, $imp, $idPlan));

        $this->promocion->promoverPorIdUs($this->idUs($user));

        return ['id_ins' => $idIns, 'programa' => $imp->nombre_programa];
    }

    /**
     * Versión abierta + plan habilitado para ella + cuenta con carnet.
     *
     * @return array{0: object, 1: array} [versión, plan]
     */
    public function validarOferta(User $user, int $idImp, int $idPlan): array
    {
        $imp = DB::table('t_imparte as imp')
            ->join('t_programa as prog', 'prog.id_programa', '=', 'imp.id_mat')
            ->where('imp.id_imp', $idImp)
            ->where('imp.estado', 1)
            ->where('prog.estado_web', 'publicado')
            ->first(['imp.id_imp', 'imp.id_mat', 'imp.imparte_fecha_fin', 'prog.nombre_programa', 'prog.formulario_id']);

        if (! $imp) {
            throw new DomainException('Este programa no está disponible para inscripción.');
        }
        if ($imp->formulario_id) {
            throw new DomainException('Este programa pide completar un formulario: inscríbete desde la página del programa en el portal.');
        }
        if ($imp->imparte_fecha_fin && $imp->imparte_fecha_fin < now()->toDateString()) {
            throw new DomainException('Este programa ya finalizó.');
        }

        $plan = $this->planesDe((int) $imp->id_imp, (int) $imp->id_mat)->firstWhere('id_plan', $idPlan);
        if (! $plan) {
            throw new DomainException('Ese plan de pago no está disponible para este programa.');
        }
        if (empty($user->ci)) {
            throw new DomainException('Completa tu perfil con tu número de carnet antes de inscribirte.');
        }

        return [$imp, $plan];
    }

    /** Crea la inscripción online y su cronograma. Llamar dentro de una transacción. */
    public function crearInscripcion(User $user, object $imp, int $idPlan): int
    {
        $idUs = $this->idUs($user) ?? $this->crearRegistroAcademico($user);

        $yaInscrito = DB::table('t_inscripcion')
            ->where('id_us', $idUs)->where('id_imp', $imp->id_imp)->where('estado', 1)
            ->lockForUpdate()->exists();
        if ($yaInscrito) {
            throw new DomainException('Ya estás inscrito en este programa.');
        }

        $idIns = (int) (DB::table('t_inscripcion')->max('id_ins') ?? 0) + 1;
        DB::table('t_inscripcion')->insert([
            'id_ins'      => $idIns,
            'id_us_reg'   => $user->id,
            'id_us'       => $idUs,
            'id_imp'      => $imp->id_imp,
            'id_plan'     => $idPlan,
            'fecha_ins'   => now()->toDateString(),
            'fecha_reg'   => now(),
            'gestion'     => now()->year,
            'periodo'     => 'I-' . now()->year,
            'estado'      => 1,
            'canal_venta' => 'online',
            'origen'      => 'panel_estudiante',
            'email'       => $user->email,
            'telefono'    => $user->telefono,
        ]);

        $this->cuotasService->generarCuotasParaInscripcion($idIns, $idPlan);

        return $idIns;
    }

    /** Planes activos de la versión; si la versión no tiene propios, los del programa. */
    public function planesDe(int $idImp, int $idPrograma): Collection
    {
        $columnas = ['p.id_plan', 'p.titulo', 'p.costo', 'p.nro_cuotas', 'p.qr_image_url'];

        $planes = DB::table('t_plan as p')
            ->join('imparticion_planes as ip', 'ip.id_plan', '=', 'p.id_plan')
            ->where('ip.id_imp', $idImp)->where('p.estado', 1)
            ->get($columnas);

        if ($planes->isEmpty()) {
            $planes = DB::table('t_plan as p')
                ->join('programa_planes as pp', 'pp.id_plan', '=', 'p.id_plan')
                ->where('pp.id_programa', $idPrograma)->where('p.estado', 1)
                ->get($columnas);
        }

        return $planes->unique('id_plan')->map(fn ($p) => [
            'id_plan'    => (int) $p->id_plan,
            'titulo'     => $p->titulo,
            'costo'      => is_numeric($p->costo) ? (float) $p->costo : null,
            'nro_cuotas' => (int) ($p->nro_cuotas ?: 1),
            'gratuito'   => PlanGratuito::costoCero($p->costo),
            'qr_url'     => $p->qr_image_url ? $this->urlArchivo($p->qr_image_url) : null,
            // Lo que se paga para inscribirse: la primera cuota o, al contado, el costo
            'primer_pago' => (float) (DB::table('t_fechapago')->where('id_plan', $p->id_plan)->where('estado', 1)
                ->orderBy('nro_pago')->value('monto_a_pagar') ?? (is_numeric($p->costo) ? $p->costo : 0)),
        ])->values();
    }

    private function crearRegistroAcademico(User $user): int
    {
        $idUs = (int) (DB::table('t_usuario')->max('id_us') ?? 0) + 1;

        DB::table('t_usuario')->insert([
            'id_us'          => $idUs,
            'id_us_reg'      => $user->id,
            'nombre'         => $user->nombre,
            'appaterno'      => $user->apellido,
            'apmaterno'      => $user->apellido_materno,
            'ci'             => $user->ci,
            'celular'        => $user->telefono,
            'email'          => $user->email,
            'password'       => Hash::make(Str::random(32)),
            'nombre_usuario' => $user->ci,
            'estado'         => 1,
            'fecha_reg'      => now(),
            'tipoestudiante' => '2',
        ]);

        return $idUs;
    }

    private function urlArchivo(?string $ruta): ?string
    {
        if (! $ruta) {
            return null;
        }
        if (Str::startsWith($ruta, ['http://', 'https://', '/'])) {
            return $ruta;
        }

        return '/storage/' . ltrim($ruta, '/');
    }
}
