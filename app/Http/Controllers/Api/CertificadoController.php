<?php

namespace App\Http\Controllers\Api;

use App\Application\Certificados\Commands\CreateCertificadoCommand;
use App\Application\Certificados\Commands\DeleteCertificadoCommand;
use App\Application\Certificados\Commands\GenerarCertificadosLoteCommand;
use App\Application\Certificados\Commands\UpdateCertificadoCommand;
use App\Application\Certificados\DTOs\CertificadoPublicoDTO;
use App\Application\Certificados\Handlers\CreateCertificadoHandler;
use App\Application\Certificados\Handlers\DeleteCertificadoHandler;
use App\Application\Certificados\Handlers\GenerarCertificadosLoteHandler;
use App\Application\Certificados\Handlers\UpdateCertificadoHandler;
use App\Application\Certificados\Queries\GetCertificadoByCodigoQuery;
use App\Application\Certificados\Queries\GetCertificadoByIdQuery;
use App\Application\Certificados\Queries\GetCertificadosQuery;
use App\Application\Certificados\QueryHandlers\GetCertificadoByCodigoQueryHandler;
use App\Application\Certificados\QueryHandlers\GetCertificadoByIdQueryHandler;
use App\Application\Certificados\QueryHandlers\GetCertificadosQueryHandler;
use App\Application\Pagos\Services\PagoCalculadorService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Certificados\StoreCertificadoRequest;
use App\Http\Requests\Certificados\UpdateCertificadoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class CertificadoController extends Controller
{
    public function __construct(
        private readonly GetCertificadosQueryHandler         $listHandler,
        private readonly GetCertificadoByIdQueryHandler      $showHandler,
        private readonly GetCertificadoByCodigoQueryHandler  $showByCodeHandler,
        private readonly CreateCertificadoHandler            $createHandler,
        private readonly UpdateCertificadoHandler            $updateHandler,
        private readonly DeleteCertificadoHandler            $deleteHandler,
        private readonly PagoCalculadorService                $calculador,
        private readonly GenerarCertificadosLoteHandler       $generarLoteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
            'query'     => $request->get('query', ''),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetCertificadosQuery(
                pagination: $pagination,
                usuarioId:  $request->filled('usuario_id') ? (int) $request->get('usuario_id') : null,
                imparteId:  $request->filled('imparte_id') ? (int) $request->get('imparte_id') : null,
                estado:     $request->filled('estado')     ? $request->get('estado')           : null,
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetCertificadoByIdQuery($id))
        );
    }

    public function showByCode(string $codigo): JsonResponse
    {
        $dto = $this->showByCodeHandler->handle(new GetCertificadoByCodigoQuery($codigo));

        return response()->json(
            auth()->check() ? $dto : CertificadoPublicoDTO::fromDTO($dto)
        );
    }

    public function store(StoreCertificadoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCertificadoCommand(
            lista_aprobado_id:       $request->integer('lista_aprobado_id'),
            plantilla_id:            $request->integer('plantilla_id'),
            usuario_id:              $request->integer('usuario_id'),
            imparte_id:              $request->integer('imparte_id'),
            nombre_en_certificado:   $request->input('nombre_en_certificado'),
            programa_en_certificado: $request->input('programa_en_certificado'),
            condicion:               $request->input('condicion'),
            nota_final:              $request->filled('nota_final')     ? (float) $request->input('nota_final')     : null,
            horas_academicas:        $request->filled('horas_academicas') ? $request->integer('horas_academicas')   : null,
            fecha_inicio_curso:      $request->input('fecha_inicio_curso'),
            fecha_fin_curso:         $request->input('fecha_fin_curso'),
            codigo_verificacion:     $request->input('codigo_verificacion'),
            qr_url:                  $request->input('qr_url'),
            archivo_url:             $request->input('archivo_url'),
            archivo_miniatura_url:   $request->input('archivo_miniatura_url'),
            estado:                  $request->input('estado', 'generado'),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateCertificadoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateCertificadoCommand(
            id:                      $id,
            nombre_en_certificado:   $request->filled('nombre_en_certificado')   ? $request->input('nombre_en_certificado')   : null,
            programa_en_certificado: $request->filled('programa_en_certificado') ? $request->input('programa_en_certificado') : null,
            qr_url:                  $request->filled('qr_url')                  ? $request->input('qr_url')                  : null,
            archivo_url:             $request->filled('archivo_url')             ? $request->input('archivo_url')             : null,
            archivo_miniatura_url:   $request->filled('archivo_miniatura_url')   ? $request->input('archivo_miniatura_url')   : null,
            estado:                  $request->filled('estado')                  ? $request->input('estado')                  : null,
            motivo_anulacion:        $request->filled('motivo_anulacion')        ? $request->input('motivo_anulacion')        : null,
            anulado_por:             $request->filled('anulado_por')             ? $request->integer('anulado_por')           : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteCertificadoCommand($id));

        return response()->json(null, 204);
    }

    public function previewPagosCompletos(Request $request): JsonResponse
    {
        $programaId = (int) $request->get('programa_id');
        $idImp = DB::table('t_programa')->where('id_programa', $programaId)->value('id_imp');

        if (! $idImp) {
            return response()->json(['message' => 'El programa no tiene una impartición asignada.'], 422);
        }

        $estudiantes = $this->getEstudiantesConPagosCompletos((int) $idImp);

        return response()->json([
            'id_imp'      => $idImp,
            'total'       => count($estudiantes),
            'estudiantes' => $estudiantes,
        ]);
    }

    public function generarPagosCompletos(Request $request): JsonResponse
    {
        $data = $request->validate([
            'programa_id'  => ['required', 'integer'],
            'plantilla_id' => ['required', 'integer'],
        ]);

        $idImp = DB::table('t_programa')->where('id_programa', $data['programa_id'])->value('id_imp');

        if (! $idImp) {
            return response()->json(['message' => 'El programa no tiene una impartición asignada.'], 422);
        }

        $estudiantes = $this->getEstudiantesConPagosCompletos((int) $idImp);

        if (empty($estudiantes)) {
            return response()->json([
                'generados'       => 0,
                'total_aprobados' => 0,
                'errores'         => [],
                'certificados'    => [],
                'message'         => 'No hay estudiantes con pagos completos.',
            ]);
        }

        $now = now()->toDateTimeString();

        $yaInscritos = DB::table('t_lista_aprobados')
            ->where('imparte_id', $idImp)
            ->whereIn('usuario_id', array_column($estudiantes, 'id_us'))
            ->pluck('usuario_id')
            ->flip()
            ->all();

        $insertar = [];
        foreach ($estudiantes as $est) {
            if (! isset($yaInscritos[$est['id_us']])) {
                $insertar[] = [
                    'imparte_id'         => $idImp,
                    'usuario_id'         => $est['id_us'],
                    'inscripcion_id'     => $est['id_ins'],
                    'condicion'          => 'aprobado',
                    'estado_certificado' => 'pendiente',
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];
            }
        }

        if (! empty($insertar)) {
            DB::table('t_lista_aprobados')->insertOrIgnore($insertar);
        }

        try {
            $resultado = $this->generarLoteHandler->handle(new GenerarCertificadosLoteCommand(
                imparteId:   (int) $idImp,
                plantillaId: (int) $data['plantilla_id'],
                usuarioIds:  array_column($estudiantes, 'id_us'),
            ));
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($resultado);
    }

    public function generarLote(Request $request): JsonResponse
    {
        $data = $request->validate([
            'imparte_id'   => ['required', 'integer'],
            'plantilla_id' => ['required', 'integer'],
        ]);

        try {
            $resultado = $this->generarLoteHandler->handle(new GenerarCertificadosLoteCommand(
                imparteId:   (int) $data['imparte_id'],
                plantillaId: (int) $data['plantilla_id'],
            ));
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($resultado, 200);
    }

    public function descargarZip(Request $request): StreamedResponse|JsonResponse
    {
        $imparteId = (int) $request->get('imparte_id');
        if (! $imparteId) {
            return response()->json(['message' => 'imparte_id requerido.'], 422);
        }

        $certs = DB::table('t_certificado')
            ->where('imparte_id', $imparteId)
            ->whereNotNull('archivo_url')
            ->where('estado', '!=', 'anulado')
            ->get();

        if ($certs->isEmpty()) {
            return response()->json(['message' => 'No hay certificados generados para esta apertura.'], 404);
        }

        $zipPath = tempnam(sys_get_temp_dir(), 'cert_zip_').'.zip';
        $zip = new ZipArchive();
        $abierto = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($abierto !== true) {
            @unlink($zipPath);

            return response()->json(['message' => 'No se pudo crear el archivo ZIP (código '.$abierto.').'], 500);
        }

        foreach ($certs as $cert) {
            $relative = ltrim(str_replace('/storage/', '', $cert->archivo_url), '/');
            $filePath = Storage::disk('public')->path($relative);
            if (! file_exists($filePath)) {
                continue;
            }
            $entryName = Str::slug($cert->nombre_en_certificado).'-'.$cert->codigo_verificacion.'.pdf';
            $zip->addFile($filePath, $entryName);
        }

        $zip->close();

        $imparte = DB::table('t_imparte')->where('id_imp', $imparteId)->first();
        $nombrePrograma = $imparte?->titulo_personalizado ?: 'certificados';
        $zipFilename = Str::slug($nombrePrograma).'-certificados.zip';

        return response()->streamDownload(function () use ($zipPath) {
            readfile($zipPath);
            @unlink($zipPath);
        }, $zipFilename, [
            'Content-Type'        => 'application/zip',
            'Content-Disposition' => 'attachment; filename="'.$zipFilename.'"',
        ]);
    }

    private function getEstudiantesConPagosCompletos(int $idImp): array
    {
        $idMat       = DB::table('t_imparte')->where('id_imp', $idImp)->value('id_mat');
        $costoMonto  = (float) (DB::table('t_programa')->where('id_imp', $idImp)->orderBy('id_us_reg')->value('costo_monto') ?? 0);
        $planDefault = DB::table('t_materia_plan')->where('id_mat', $idMat)->value('id_plan');

        $inscripciones = DB::table('t_inscripcion as ins')
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('ins.id_us', '=', 'u.id_us')
                  ->whereRaw('u.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us)');
            })
            ->select(
                'ins.id_ins', 'ins.id_us', 'ins.id_plan',
                DB::raw("TRIM(CONCAT(COALESCE(u.appaterno,''), ' ', COALESCE(u.nombre,''))) as nombre")
            )
            ->where('ins.id_imp', $idImp)
            ->where('ins.estado', 1)
            ->get();

        if ($inscripciones->isEmpty()) {
            return [];
        }

        $idsIns  = $inscripciones->pluck('id_ins')->all();
        $idsUs   = $inscripciones->pluck('id_us')->unique()->values()->all();
        $planIds = $inscripciones->pluck('id_plan')->filter()->push($planDefault)->filter()->unique()->values()->all();

        
        $todosPagos = DB::table('t_pago')
            ->whereIn('id_us', $idsUs)
            ->where('estado', 1)
            ->get(['id_us', 'id_ins', 'id_fechapago', 'pago_extra', 'monto_pagado']);

        
        $todasCuotasPorPlan = $planIds
            ? DB::table('t_fechapago')->whereIn('id_plan', $planIds)->where('estado', 1)->get(['id_plan', 'id_fechapago', 'monto_a_pagar'])->groupBy('id_plan')
            : collect();

        $calificados = [];

        foreach ($inscripciones as $ins) {
            $planId = $ins->id_plan ?: $planDefault;

            
            
            
            
            $cuotasDelPlan    = $planId && $todasCuotasPorPlan->has($planId) ? $todasCuotasPorPlan->get($planId) : collect();
            $cuotasIdsDelPlan = $cuotasDelPlan->pluck('id_fechapago')->all();

            $pagosDeEsteEstudiante = $todosPagos
                ->where('id_us', $ins->id_us)
                ->filter(function ($p) use ($ins, $cuotasIdsDelPlan) {
                    if ($p->id_ins === $ins->id_ins) return true;
                    if ($p->id_ins !== null) return false;
                    if (in_array($p->id_fechapago, $cuotasIdsDelPlan, true)) return true;
                    return (int) $p->pago_extra === 1 && $p->id_fechapago === null;
                });

            
            
            
            if (! $planId) {
                $resumenSinPlan = $this->calculador->calcularConCostoFijo($costoMonto, $pagosDeEsteEstudiante);
                $completo = $resumenSinPlan->pendiente !== null && $resumenSinPlan->pendiente <= 0.0;
            } else {




                $cuotasCubiertasIds = $pagosDeEsteEstudiante
                    ->whereNotNull('id_fechapago')
                    ->where('pago_extra', '!=', 1)
                    ->pluck('id_fechapago')
                    ->unique()
                    ->intersect($cuotasIdsDelPlan)
                    ->values()
                    ->all();
                $cuotasTotales = $cuotasDelPlan->count();




                $montoPendienteCuotas = (float) $cuotasDelPlan
                    ->filter(fn ($c) => ! in_array($c->id_fechapago, $cuotasCubiertasIds, true))
                    ->sum(fn ($c) => (float) $c->monto_a_pagar);

                $totalAnticipos = (float) $pagosDeEsteEstudiante
                    ->where('pago_extra', 1)
                    ->sum(fn ($p) => (float) $p->monto_pagado);

                $completo = $cuotasTotales > 0 && max(0.0, $montoPendienteCuotas - $totalAnticipos) <= 0.0;
            }

            if ($completo) {
                $calificados[] = [
                    'id_us'  => (int) $ins->id_us,
                    'id_ins' => (int) $ins->id_ins,
                    'nombre' => trim($ins->nombre),
                ];
            }
        }

        return $calificados;
    }
}
