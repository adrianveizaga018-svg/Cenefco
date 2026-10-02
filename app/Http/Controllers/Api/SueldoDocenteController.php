<?php

namespace App\Http\Controllers\Api;

use App\Application\SueldosDocentes\Commands\AnularPagoSueldoCommand;
use App\Application\SueldosDocentes\Commands\CreateSueldoDocenteCommand;
use App\Application\SueldosDocentes\Commands\DeleteSueldoDocenteCommand;
use App\Application\SueldosDocentes\Commands\RegistrarPagoSueldoCommand;
use App\Application\SueldosDocentes\Commands\RegistrarPagoSueldoLoteCommand;
use App\Application\SueldosDocentes\Commands\UpdateSueldoDocenteCommand;
use App\Application\SueldosDocentes\Handlers\AnularPagoSueldoHandler;
use App\Application\SueldosDocentes\Handlers\CreateSueldoDocenteHandler;
use App\Application\SueldosDocentes\Handlers\DeleteSueldoDocenteHandler;
use App\Application\SueldosDocentes\Handlers\RegistrarPagoSueldoHandler;
use App\Application\SueldosDocentes\Handlers\RegistrarPagoSueldoLoteHandler;
use App\Application\SueldosDocentes\Handlers\UpdateSueldoDocenteHandler;
use App\Application\SueldosDocentes\Queries\GetSueldoDocenteByIdQuery;
use App\Application\SueldosDocentes\Queries\GetSueldosDocentesQuery;
use App\Application\SueldosDocentes\QueryHandlers\GetSueldoDocenteByIdQueryHandler;
use App\Application\SueldosDocentes\QueryHandlers\GetSueldosDocentesQueryHandler;
use App\Domain\SueldosDocentes\Contracts\SueldoDocenteRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\SueldosDocentes\StorePagoSueldoLoteRequest;
use App\Http\Requests\SueldosDocentes\StorePagoSueldoRequest;
use App\Http\Requests\SueldosDocentes\StoreSueldoDocenteRequest;
use App\Http\Requests\SueldosDocentes\UpdateSueldoDocenteRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SueldoDocenteController extends Controller
{
    public function __construct(
        private readonly GetSueldosDocentesQueryHandler  $getSueldosHandler,
        private readonly GetSueldoDocenteByIdQueryHandler $getSueldoByIdHandler,
        private readonly CreateSueldoDocenteHandler       $createHandler,
        private readonly UpdateSueldoDocenteHandler       $updateHandler,
        private readonly DeleteSueldoDocenteHandler       $deleteHandler,
        private readonly RegistrarPagoSueldoHandler       $pagoHandler,
        private readonly RegistrarPagoSueldoLoteHandler   $pagoLoteHandler,
        private readonly AnularPagoSueldoHandler          $anularPagoHandler,
        private readonly SueldoDocenteRepositoryInterface $repository,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
            'query'     => $request->get('query', ''),
        ]);

        return response()->json(
            $this->getSueldosHandler->handle(new GetSueldosDocentesQuery(
                pagination:  $pagination,
                periodo:     $request->filled('periodo') ? $request->get('periodo') : null,
                gestion:     $request->filled('gestion') ? $request->get('gestion') : null,
                estadoPago:  $request->filled('estado_pago') ? $request->get('estado_pago') : null,
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getSueldoByIdHandler->handle(new GetSueldoDocenteByIdQuery($id))
        );
    }

    public function store(StoreSueldoDocenteRequest $request): JsonResponse
    {
        $archivoPdf = null;
        if ($request->hasFile('archivo_pdf')) {
            $archivoPdf = $request->file('archivo_pdf')->store('sueldos-docentes', 'public');
        }

        $dto = $this->createHandler->handle(new CreateSueldoDocenteCommand(
            id_us:       $request->integer('id_us'),
            concepto:    $request->string('concepto'),
            monto_total: (float) $request->monto_total,
            periodo:     $request->input('periodo'),
            gestion:     $request->filled('gestion') ? $request->integer('gestion') : null,
            id_imp:      $request->filled('id_imp') ? $request->integer('id_imp') : null,
            observacion: $request->input('observacion'),
            archivo_pdf: $archivoPdf,
            id_programa: $request->filled('id_programa') ? $request->integer('id_programa') : null,
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateSueldoDocenteRequest $request, int $id): JsonResponse
    {
        $archivoPdf         = null;
        $archivoPdfAnterior = null;

        if ($request->hasFile('archivo_pdf')) {
            $archivoPdfAnterior = $this->repository->findById($id)->archivo_pdf;
            $archivoPdf         = $request->file('archivo_pdf')->store('sueldos-docentes', 'public');
        }

        $dto = $this->updateHandler->handle(new UpdateSueldoDocenteCommand(
            id:                   $id,
            id_us:                $request->integer('id_us'),
            concepto:             $request->string('concepto'),
            monto_total:          (float) $request->monto_total,
            periodo:              $request->input('periodo'),
            gestion:              $request->filled('gestion') ? $request->integer('gestion') : null,
            id_imp:               $request->filled('id_imp') ? $request->integer('id_imp') : null,
            observacion:          $request->input('observacion'),
            archivo_pdf:          $archivoPdf,
            archivo_pdf_anterior: $archivoPdfAnterior,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteSueldoDocenteCommand($id));
        return response()->json(null, 204);
    }

    public function storePago(StorePagoSueldoRequest $request, int $id): JsonResponse
    {
        $archivoPath = null;
        if ($request->hasFile('comprobante_archivo')) {
            $archivoPath = $request->file('comprobante_archivo')
                ->store("comprobantes-pagos-sueldos/{$id}", 'public');
        }

        $dto = $this->pagoHandler->handle(new RegistrarPagoSueldoCommand(
            idSueldo:           $id,
            montoPagado:        (float) $request->monto_pagado,
            fechaPago:          $request->fecha_pago,
            nroComprobante:     $request->input('nro_comprobante'),
            observacion:        $request->input('observacion'),
            comprobanteArchivo: $archivoPath,
        ));

        return response()->json($dto, 201);
    }

    public function storePagoLote(StorePagoSueldoLoteRequest $request, int $id): JsonResponse
    {
        $pagos = $this->pagoLoteHandler->handle(new RegistrarPagoSueldoLoteCommand(
            idSueldo: $id,
            cuotas:   $request->cuotas,
        ));

        return response()->json($pagos, 201);
    }

    public function destroyPago(int $id, int $pagoId): JsonResponse
    {
        $this->anularPagoHandler->handle(new AnularPagoSueldoCommand(
            idSueldo: $id,
            pagoId:   $pagoId,
        ));

        return response()->json(null, 204);
    }

    public function docentes(): JsonResponse
    {
        return response()->json($this->repository->docentes());
    }

    public function imparticiones(): JsonResponse
    {
        return response()->json($this->repository->imparticiones());
    }
}
