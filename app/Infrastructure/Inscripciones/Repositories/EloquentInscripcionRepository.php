<?php

namespace App\Infrastructure\Inscripciones\Repositories;

use App\Application\Inscripciones\DTOs\DocumentosInscripcionDTO;
use App\Application\Inscripciones\DTOs\InscripcionDetalleDTO;
use App\Application\Inscripciones\DTOs\InscripcionDTO;
use App\Application\Pagos\Services\PagoCalculadorService;
use App\Domain\CompromisosCobro\Contracts\CompromisoCobroRepositoryInterface;
use App\Domain\Cursos\Contracts\CursoRepositoryInterface;
use App\Domain\Formularios\Services\CampoRaizResolver;
use App\Domain\Inscripciones\Contracts\InscripcionRepositoryInterface;
use App\Domain\Inscripciones\Exceptions\InscripcionNotFoundException;
use App\Infrastructure\Inscripciones\Models\Inscripcion;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentInscripcionRepository implements InscripcionRepositoryInterface
{
    public function __construct(
        private readonly PagoCalculadorService              $calculador,
        private readonly CursoRepositoryInterface            $cursoRepository,
        private readonly CompromisoCobroRepositoryInterface  $compromisoCobroRepository,
    ) {}

    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?int $idUs, ?int $idImp, ?int $programaId, ?string $periodo, ?string $gestion, ?array $idImpPermitidos = null): array
    {
        $q = DB::table('t_inscripcion as ins')
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('ins.id_us', '=', 'u.id_us')
                  ->whereRaw('u.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us)');
            })
            ->leftJoin('t_imparte as imp', function ($j) {
                $j->on('ins.id_imp', '=', 'imp.id_imp')
                  ->whereRaw('imp.id_us_reg = (SELECT MIN(i2.id_us_reg) FROM t_imparte i2 WHERE i2.id_imp = imp.id_imp)');
            })
            ->leftJoin('t_materia as m', function ($j) {
                $j->on('imp.id_mat', '=', 'm.id_mat')
                  ->whereRaw('m.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = m.id_mat)');
            })
            ->leftJoin('t_usuario as doc', function ($j) {
                $j->on('imp.id_us', '=', 'doc.id_us')
                  ->whereRaw('doc.id_us_reg = (SELECT MIN(d2.id_us_reg) FROM t_usuario d2 WHERE d2.id_us = doc.id_us)');
            })
            ->select([
                'ins.id_ins', 'ins.id_us', 'ins.id_imp', 'ins.fecha_ins',
                'ins.periodo', 'ins.gestion', 'ins.observacion_ins', 'ins.estado',
                'ins.canal_venta', 'ins.id_vendedor',
                DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.appaterno,''))) as estudiante_nombre"),
                'u.ci as estudiante_ci',
                'u.email as estudiante_email',
                'u.celular as estudiante_celular',
                DB::raw("COALESCE((SELECT p2.nombre_programa FROM t_programa p2 WHERE p2.id_imp = imp.id_imp ORDER BY p2.id_us_reg LIMIT 1), m.nombremat) as materia_nombre"),
                DB::raw("COALESCE((SELECT p2.slug FROM t_programa p2 WHERE p2.id_imp = imp.id_imp ORDER BY p2.id_us_reg LIMIT 1), m.sigla) as materia_sigla"),
                'imp.paralelo',
                DB::raw("TRIM(CONCAT(COALESCE(doc.nombre,''), ' ', COALESCE(doc.appaterno,''))) as docente_nombre"),
                
                
                
                
                
                DB::raw("CASE WHEN ins.id_plan IS NULL THEN
                    (SELECT p2.costo_monto FROM t_programa p2 WHERE p2.id_imp = imp.id_imp ORDER BY p2.id_us_reg LIMIT 1)
                    ELSE NULL END as curso_costo_monto"),
                
                
                
                
                
                DB::raw("(SELECT COUNT(*) FROM t_pago p WHERE p.id_us = ins.id_us AND p.estado = 1 AND (
                    p.id_ins = ins.id_ins
                    OR (p.id_ins IS NULL AND (
                        p.id_fechapago IN (SELECT fp.id_fechapago FROM t_fechapago fp WHERE fp.id_plan = ins.id_plan)
                        OR (p.pago_extra = 1 AND p.id_fechapago IS NULL AND EXISTS (SELECT 1 FROM t_inscripcion ins2 WHERE ins2.id_us = ins.id_us AND ins2.id_imp = ins.id_imp AND ins2.id_ins = ins.id_ins))
                    ))
                )) as cuotas_pagadas"),
                DB::raw("(SELECT COALESCE(SUM(CAST(p2.monto_pagado AS DECIMAL(12,2))), 0) FROM t_pago p2 WHERE p2.id_us = ins.id_us AND p2.estado = 1 AND (
                    p2.id_ins = ins.id_ins
                    OR (p2.id_ins IS NULL AND (
                        p2.id_fechapago IN (SELECT fp2.id_fechapago FROM t_fechapago fp2 WHERE fp2.id_plan = ins.id_plan)
                        OR (p2.pago_extra = 1 AND p2.id_fechapago IS NULL AND EXISTS (SELECT 1 FROM t_inscripcion ins2 WHERE ins2.id_us = ins.id_us AND ins2.id_imp = ins.id_imp AND ins2.id_ins = ins.id_ins))
                    ))
                )) as total_pagado"),
                DB::raw("EXISTS (SELECT 1 FROM t_lista_aprobados la WHERE la.imparte_id = ins.id_imp AND la.usuario_id = ins.id_us) as es_participante"),
            ]);

        if (! $conInactivos) {
            $q->where('ins.estado', 1);
        }

        if ($idUs !== null) {
            $q->where('ins.id_us', $idUs);
        }

        if ($idImp !== null) {
            $q->where('ins.id_imp', $idImp);
        }

        if ($idImpPermitidos !== null) {
            $q->whereIn('ins.id_imp', $idImpPermitidos);
        }

        if ($programaId !== null) {
            $idImpFromPrograma = DB::table('t_programa')
                ->where('id_programa', $programaId)
                ->orderBy('id_us_reg')
                ->value('id_imp');
            if ($idImpFromPrograma !== null) {
                $q->where('ins.id_imp', $idImpFromPrograma);
            } else {
                
                $q->whereRaw('1 = 0');
            }
        }

        if ($periodo !== null) {
            $q->where('ins.periodo', $periodo);
        }

        if ($gestion !== null) {
            $q->where('ins.gestion', $gestion);
        }

        if ($pagination->query) {
            $search = $pagination->query;
            $q->where(function ($sq) use ($search) {
                $sq->where('u.nombre', 'like', "%{$search}%")
                   ->orWhere('u.appaterno', 'like', "%{$search}%")
                   ->orWhere('u.ci', 'like', "%{$search}%")
                   ->orWhere('m.nombremat', 'like', "%{$search}%");
            });
        }

        $total = $q->count();
        $items = $q->orderByDesc('ins.fecha_ins')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get();

        return [
            'data'  => $items->map(fn ($row) => InscripcionDTO::fromModel($row))->all(),
            'total' => $total,
        ];
    }

    public function findById(int $id): InscripcionDTO
    {
        $ins = Inscripcion::where('id_ins', $id)->first();

        if (! $ins) {
            throw new InscripcionNotFoundException($id);
        }

        return InscripcionDTO::fromModel($ins);
    }

    public function ciDeUsuario(int $idUs): ?string
    {
        return DB::table('t_usuario')->where('id_us', $idUs)->value('ci');
    }

    public function findDetalleById(int $id): InscripcionDetalleDTO
    {
        $ins = DB::table('t_inscripcion as ins')
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('ins.id_us', '=', 'u.id_us')
                  ->whereRaw('u.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us)');
            })
            ->leftJoin('t_imparte as imp', function ($j) {
                $j->on('ins.id_imp', '=', 'imp.id_imp')
                  ->whereRaw('imp.id_us_reg = (SELECT MIN(i2.id_us_reg) FROM t_imparte i2 WHERE i2.id_imp = imp.id_imp)');
            })
            ->leftJoin('t_materia as m', function ($j) {
                $j->on('imp.id_mat', '=', 'm.id_mat')
                  ->whereRaw('m.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = m.id_mat)');
            })
            ->leftJoin('t_usuario as doc', function ($j) {
                $j->on('imp.id_us', '=', 'doc.id_us')
                  ->whereRaw('doc.id_us_reg = (SELECT MIN(d2.id_us_reg) FROM t_usuario d2 WHERE d2.id_us = doc.id_us)');
            })
            ->leftJoin('t_programa as prog', function ($j) {
                $j->on('prog.id_imp', '=', 'ins.id_imp')
                  ->whereRaw('prog.id_us_reg = (SELECT MIN(p2.id_us_reg) FROM t_programa p2 WHERE p2.id_imp = prog.id_imp)');
            })
            ->select([
                'ins.*',
                DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.appaterno,''))) as estudiante_nombre"),
                'u.appaterno as est_appaterno', 'u.apmaterno as est_apmaterno',
                'u.ci as est_ci', 'u.email as est_email', 'u.celular as est_celular',
                'u.ciudad as est_ciudad', 'u.titulo_academico as est_titulo', 'u.tipoestudiante',
                'prog.id_programa',
                DB::raw("COALESCE((SELECT p2.nombre_programa FROM t_programa p2 WHERE p2.id_imp = imp.id_imp ORDER BY p2.id_us_reg LIMIT 1), m.nombremat) as materia_nombre"),
                DB::raw("COALESCE((SELECT p2.slug FROM t_programa p2 WHERE p2.id_imp = imp.id_imp ORDER BY p2.id_us_reg LIMIT 1), m.sigla) as materia_sigla"),
                DB::raw("(SELECT p2.slug FROM t_programa p2 WHERE p2.id_imp = imp.id_imp ORDER BY p2.id_us_reg LIMIT 1) as programa_slug"),
                'm.carga_horaria as materia_horas', 'm.semestre as materia_semestre',
                'imp.paralelo', 'imp.cupo', 'imp.nro_resolucion_hcu',
                'imp.gestion as imp_gestion', 'imp.id_mat',
                DB::raw("TRIM(CONCAT(COALESCE(doc.nombre,''), ' ', COALESCE(doc.appaterno,''))) as docente_nombre"),
                'doc.titulo_academico as docente_titulo', 'doc.email as docente_email',
                DB::raw("(SELECT p2.costo_monto FROM t_programa p2 WHERE p2.id_imp = imp.id_imp ORDER BY p2.id_us_reg LIMIT 1) as curso_costo_monto"),
            ])
            ->where('ins.id_ins', $id)
            ->first();

        if (! $ins) {
            throw new InscripcionNotFoundException($id);
        }

        
        
        
        if (is_string($ins->documentos)) {
            $ins->documentos = json_decode($ins->documentos, true);
        }
        if (is_string($ins->campos_extra)) {
            $ins->campos_extra = json_decode($ins->campos_extra, true);
        }

        
        
        $planId = $ins->id_plan;

        $pagosQuery = DB::table('t_pago as p')
            ->leftJoin('t_fechapago as fp', 'p.id_fechapago', '=', 'fp.id_fechapago')
            ->leftJoin('t_plan as pl', 'fp.id_plan', '=', 'pl.id_plan')
            ->leftJoin('tipos_banco as tb', 'tb.id', '=', 'p.tipo_banco_id')
            ->leftJoin('usuarios as cj', 'p.id_us_cajero', '=', 'cj.id')
            ->select([
                'p.id_pago', 'p.monto_pagado', 'p.fecha_deposito', 'p.nro_boleta_bancaria',
                'p.observacion_pago', 'p.estado as pago_estado', 'p.pago_extra',
                'p.metodo_pago', 'p.id_us_cajero', 'p.comprobante_archivo', 'p.estado_verificacion',
                'p.nota_verificacion', 'p.monto_descuento_extra as monto_descuento', 'p.motivo_descuento',
                'p.tipo_banco_id', 'tb.nombre as tipo_banco_nombre',
                DB::raw("NULLIF(TRIM(CONCAT(COALESCE(cj.nombre,''), ' ', COALESCE(cj.apellido,''))), '') as cajero_nombre"),
                'fp.id_fechapago', 'fp.nro_pago as cuota_nro', 'fp.monto_a_pagar as cuota_monto',
                'fp.tipo_tramite', 'fp.fecha_inicio as cuota_fecha_inicio', 'fp.fecha_fin as cuota_fecha_fin',
                'pl.id_plan', 'pl.titulo as plan_titulo', 'pl.convenio as plan_convenio',
                'pl.nro_cuotas as plan_nro_cuotas', 'pl.costo as plan_costo',
            ])
            ->where('p.id_us', $ins->id_us)
            ->orderBy('p.fecha_deposito');

        
        
        
        
        
        
        
        $idImp = $ins->id_imp;
        $pagosQuery->where(function ($q) use ($id, $planId, $idImp) {
            $q->where('p.id_ins', $id);

            if ($planId) {
                $q->orWhere(function ($sub) use ($planId, $idImp, $id) {
                    $sub->whereNull('p.id_ins')
                        ->where(function ($sub2) use ($planId, $idImp, $id) {
                            $sub2->where('fp.id_plan', $planId)
                                 ->orWhere(function ($sub3) use ($idImp, $id) {
                                     $sub3->whereNull('p.id_fechapago')
                                          ->where('p.pago_extra', 1)
                                          ->whereExists(function ($ex) use ($idImp, $id) {
                                              $ex->from('t_inscripcion as ins2')
                                                 ->whereColumn('ins2.id_us', 'p.id_us')
                                                 ->where('ins2.id_imp', $idImp)
                                                 ->where('ins2.id_ins', $id);
                                          });
                                 });
                        });
                });
            }
        });

        $pagos = $pagosQuery->get();

        
        
        
        if (! $planId && $ins->id_plan !== null) {
            $planId = $pagos->whereNotNull('id_plan')->first()?->id_plan;
        }

        $plan         = $planId ? DB::table('t_plan')->where('id_plan', $planId)->first() : null;
        $todasCuotas  = $planId
            ? DB::table('t_fechapago')->where('id_plan', $planId)->orderBy('id_fechapago')->get()
            : collect();

        $documentos = DB::table('t_documento as d')
            ->leftJoin('t_fechadoc as fd', 'd.id_fechadoc', '=', 'fd.id_fechadoc')
            ->select([
                'd.id_documento', 'd.documento_digital', 'd.dejo_documento_fisico',
                'd.observacion_doc', 'd.estado', 'fd.tipo_documento', 'fd.nro_doc', 'fd.obligatorio as doc_obligatorio',
            ])
            ->where('d.id_us', $ins->id_us)
            ->get();

        $devoluciones = DB::table('web_devolucion')
            ->where('id_ins', $id)
            ->orderByDesc('created_at')
            ->get();

        $documentosEstudiante = $this->buscarDocumentosEstudiante($id);

        $pagosActivos = $pagos->where('pago_estado', 1);
        $resumen      = $this->calculador->calcular($planId ? (int) $planId : null, $pagosActivos);

        $docentes = $ins->id_imp
            ? $this->cursoRepository->docentesByImp((int) $ins->id_imp)
            : [];

        $formularioCampos = $ins->id_imp ? $this->campoExtraFormularioDefinidos((int) $ins->id_imp) : [];
        $ins->campos_extra_resueltos = $this->resolverCamposExtra($ins->campos_extra ?? null, $formularioCampos);

        $compromisoCobro = $this->compromisoCobroRepository->findAbiertoPorInscripcion($id);

        return new InscripcionDetalleDTO(
            inscripcion:   $ins,
            pagos:         $pagos->values()->all(),
            plan:          $plan,
            todas_cuotas:  $todasCuotas->values()->all(),
            documentos:    $documentos->values()->all(),
            devoluciones:  $devoluciones->values()->all(),
            resumen:       $resumen,
            docentes:      $docentes,
            documentos_estudiante: $documentosEstudiante,
            formulario_campos: $formularioCampos,
            compromiso_cobro: $compromisoCobro,
        );
    }

    private function campoExtraFormularioDefinidos(int $idImp): array
    {
        $formularioId = DB::table('t_programa')
            ->where('id_imp', $idImp)
            ->orderBy('id_us_reg')
            ->value('formulario_id');

        if (! $formularioId) {
            return [];
        }

        $camposJson = DB::table('web_formulario')->where('id', $formularioId)->value('campos');
        $campos = is_string($camposJson) ? (json_decode($camposJson, true) ?? []) : [];

        return array_values(array_filter(array_map(function ($campo) {
            $nombre = $campo['nombre_campo'] ?? null;
            if (! $nombre) return null;
            $campo['clave_raiz'] = CampoRaizResolver::resolverClave($nombre, $campo['rol_identidad'] ?? null);
            return $campo;
        }, $campos)));
    }

    private function resolverCamposExtra(?array $camposExtra, array $formularioCampos): array
    {
        if (empty($camposExtra)) {
            return [];
        }

        $porNombre = [];
        foreach ($formularioCampos as $campo) {
            $porNombre[$campo['nombre_campo']] = $campo;
        }

        $resultado = [];
        foreach ($camposExtra as $nombreCampo => $valor) {
            if ($valor === null || $valor === '') continue;
            $def = $porNombre[$nombreCampo] ?? null;
            $resultado[] = [
                'nombre_campo' => $nombreCampo,
                'etiqueta'     => $def['etiqueta'] ?? $nombreCampo,
                'tipo'         => $def['tipo'] ?? 'text',
                'valor'        => $valor,
            ];
        }

        return $resultado;
    }

    private function buscarDocumentosEstudiante(int $idIns): array
    {
        $ins = DB::table('t_inscripcion as ins')
            ->leftJoin('t_imparte as imp', 'imp.id_imp', '=', 'ins.id_imp')
            ->where('ins.id_ins', $idIns)
            ->whereNotNull('ins.documentos')
            ->select([
                'ins.id_ins',
                'ins.id_imp',
                'ins.documentos',
                'ins.fecha_reg',
                DB::raw("(SELECT p2.nombre_programa FROM t_programa p2 WHERE p2.id_imp = imp.id_imp ORDER BY p2.id_us_reg LIMIT 1) as nombre_programa"),
            ])
            ->first();

        if (! $ins) {
            return [];
        }

        $docs = is_string($ins->documentos)
            ? json_decode($ins->documentos, true)
            : (array) $ins->documentos;

        $archivos = array_filter($docs ?? []);

        if (empty($archivos)) {
            return [];
        }

        return [[
            'origen'    => 'inscripcion',
            'origen_id' => $ins->id_ins,
            'programa'  => $ins->nombre_programa,
            'fecha'     => $ins->fecha_reg,
            'archivos'  => $archivos,
        ]];
    }

    public function getDocumentos(int $idIns): DocumentosInscripcionDTO
    {
        $ins = Inscripcion::where('id_ins', $idIns)->first();

        if (! $ins) {
            throw new InscripcionNotFoundException($idIns);
        }

        $planId = $ins->id_plan;
        if (! $planId) {
            $idMat  = DB::table('t_imparte')->where('id_imp', $ins->id_imp)->value('id_mat');
            $planId = DB::table('t_materia_plan')->where('id_mat', $idMat)->value('id_plan');
        }

        if (! $planId) {
            return new DocumentosInscripcionDTO(plan_id: null, requeridos: []);
        }

        $requeridos = DB::table('t_fechadoc')
            ->where('id_plandoc', $planId)
            ->where('estado', 1)
            ->orderBy('nro_doc')
            ->get();

        $presentados = DB::table('t_documento as d')
            ->leftJoin('t_fechadoc as fd', 'd.id_fechadoc', '=', 'fd.id_fechadoc')
            ->select([
                'd.id_documento', 'd.id_fechadoc', 'd.documento_digital',
                'd.dejo_documento_fisico', 'd.fecha_dejo_fisico',
                'd.observacion_doc', 'd.estado', 'fd.tipo_documento', 'fd.nro_doc',
            ])
            ->where('d.id_us', $ins->id_us)
            ->get()
            ->keyBy('id_fechadoc');

        $resultado = $requeridos->map(function ($req) use ($presentados) {
            $doc = $presentados->get($req->id_fechadoc);
            return [
                'id_fechadoc'       => $req->id_fechadoc,
                'nro_doc'           => $req->nro_doc,
                'tipo_documento'    => $req->tipo_documento,
                'obligatorio'       => $req->obligatorio,
                'fecha_inicio'      => $req->fecha_inicio,
                'fecha_fin'         => $req->fecha_fin,
                'presentado'        => $doc !== null && $doc->estado == 1,
                'id_documento'      => $doc?->id_documento,
                'documento_digital' => $doc?->documento_digital,
                'dejo_fisico'       => $doc?->dejo_documento_fisico ?? 0,
                'fecha_entrega'     => $doc?->fecha_dejo_fisico,
                'observacion'       => $doc?->observacion_doc,
            ];
        })->values()->all();

        return new DocumentosInscripcionDTO(plan_id: $planId, requeridos: $resultado);
    }

    public function upsertDocumento(int $idUs, int $idFechadoc, array $data): object
    {
        $existente = DB::table('t_documento')
            ->where('id_us', $idUs)
            ->where('id_fechadoc', $idFechadoc)
            ->first();

        if ($existente) {
            DB::table('t_documento')
                ->where('id_documento', $existente->id_documento)
                ->update(array_merge($data, ['estado' => 1]));

            return DB::table('t_documento')->where('id_documento', $existente->id_documento)->first();
        }

        $idDoc = DB::table('t_documento')->insertGetId(array_merge([
            'id_us'       => $idUs,
            'id_fechadoc' => $idFechadoc,
            'num_documento' => 0,
            'fecha_reg'   => now(),
            'estado'      => 1,
            'per_modificar' => 0,
        ], $data));

        return DB::table('t_documento')->where('id_documento', $idDoc)->first();
    }

    public function existeInscripcionActiva(int $idUs, int $idImp): bool
    {
        return DB::table('t_inscripcion')
            ->where('id_us', $idUs)
            ->where('id_imp', $idImp)
            ->where('estado', 1)
            ->exists();
    }

    public function verificarPagoCompleto(int $id): array
    {
        $ins = DB::table('t_inscripcion')->where('id_ins', $id)->first();

        if (! $ins) {
            throw new InscripcionNotFoundException($id);
        }

        $costoMonto = (float) (DB::table('t_programa')
            ->where('id_imp', $ins->id_imp)
            ->orderBy('id_us_reg')
            ->value('costo_monto') ?? 0);

        $planId = $ins->id_plan ? (int) $ins->id_plan : null;

        $pagosActivos = $this->pagosActivosParaVerificacionPago($id, (int) $ins->id_us, (int) $ins->id_imp, $planId);
        $resumen      = $planId
            ? $this->calculador->calcular($planId, $pagosActivos)
            : $this->calculador->calcularConCostoFijo($costoMonto, $pagosActivos);

        $pagoCompleto = $resumen->pendiente !== null && $resumen->pendiente <= 0.0;

        return [
            'id_us'         => (int) $ins->id_us,
            'id_imp'        => (int) $ins->id_imp,
            'pago_completo' => $pagoCompleto,
            'pendiente'     => $resumen->pendiente,
        ];
    }

    private function pagosActivosParaVerificacionPago(int $idIns, int $idUs, int $idImp, ?int $planId): \Illuminate\Support\Collection
    {
        $q = DB::table('t_pago as p')
            ->leftJoin('t_fechapago as fp', 'p.id_fechapago', '=', 'fp.id_fechapago')
            ->where('p.id_us', $idUs)
            ->where('p.estado', 1);

        $q->where(function ($w) use ($idIns, $planId, $idImp) {
            $w->where('p.id_ins', $idIns);

            if ($planId) {
                $w->orWhere(function ($sub) use ($planId, $idImp, $idIns) {
                    $sub->whereNull('p.id_ins')
                        ->where(function ($sub2) use ($planId, $idImp, $idIns) {
                            $sub2->where('fp.id_plan', $planId)
                                 ->orWhere(function ($sub3) use ($idImp, $idIns) {
                                     $sub3->whereNull('p.id_fechapago')
                                          ->where('p.pago_extra', 1)
                                          ->whereExists(function ($ex) use ($idImp, $idIns) {
                                              $ex->from('t_inscripcion as ins2')
                                                 ->whereColumn('ins2.id_us', 'p.id_us')
                                                 ->where('ins2.id_imp', $idImp)
                                                 ->where('ins2.id_ins', $idIns);
                                          });
                                 });
                        });
                });
            }
        });

        return $q->get(['p.id_fechapago', 'p.pago_extra', 'p.monto_pagado']);
    }

    public function create(array $data): InscripcionDTO
    {
        $ins = Inscripcion::create($data);
        return InscripcionDTO::fromModel($ins);
    }

    public function update(int $id, array $data): InscripcionDTO
    {
        $ins = Inscripcion::where('id_ins', $id)->first();

        if (! $ins) {
            throw new InscripcionNotFoundException($id);
        }

        $ins->update($data);
        return InscripcionDTO::fromModel($ins->fresh());
    }

    public function delete(int $id): void
    {
        $ins = Inscripcion::where('id_ins', $id)->first();

        if (! $ins) {
            throw new InscripcionNotFoundException($id);
        }

        $ins->delete();
    }
}
