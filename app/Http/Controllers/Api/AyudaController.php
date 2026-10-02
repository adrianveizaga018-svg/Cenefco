<?php

namespace App\Http\Controllers\Api;

use App\Application\Ayudas\Commands\CreateAyudaCommand;
use App\Application\Ayudas\Commands\DeleteAyudaCommand;
use App\Application\Ayudas\Commands\UpdateAyudaCommand;
use App\Application\Ayudas\Handlers\CreateAyudaHandler;
use App\Application\Ayudas\Handlers\DeleteAyudaHandler;
use App\Application\Ayudas\Handlers\UpdateAyudaHandler;
use App\Application\Ayudas\Queries\GetAyudaByIdQuery;
use App\Application\Ayudas\Queries\GetAyudasQuery;
use App\Application\Ayudas\QueryHandlers\GetAyudaByIdQueryHandler;
use App\Application\Ayudas\QueryHandlers\GetAyudasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ayudas\StoreAyudaRequest;
use App\Http\Requests\Ayudas\UpdateAyudaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AyudaController extends Controller
{
    public function __construct(
        private readonly GetAyudasQueryHandler    $getAyudasHandler,
        private readonly GetAyudaByIdQueryHandler $getByIdHandler,
        private readonly CreateAyudaHandler       $createHandler,
        private readonly UpdateAyudaHandler       $updateHandler,
        private readonly DeleteAyudaHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->integer('pageIndex', 1),
            'pageSize'  => $request->integer('pageSize', 15),
        ]);

        return response()->json(
            $this->getAyudasHandler->handle(new GetAyudasQuery(
                pagination:   $pagination,
                idUs:         $request->integer('idUs') ?: null,
                gestion:      $request->input('gestion'),
                conInactivos: $request->boolean('conInactivos'),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getByIdHandler->handle(new GetAyudaByIdQuery($id))
        );
    }

    public function store(StoreAyudaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateAyudaCommand(
            idAyuda:         $request->integer('id_ayuda'),
            idUsReg:         $request->integer('id_us_reg'),
            numAyuda:        $request->input('num_ayuda'),
            idUs:            $request->integer('id_us'),
            gestion:         $request->input('gestion'),
            montoPagado:     $request->input('monto_pagado'),
            nroRecibo:       $request->input('nro_recibo'),
            fechaRecibo:     $request->input('fecha_recibo'),
            observacionPago: $request->input('observacion_pago'),
            idCategoriatipo: $request->input('id_categoriatipoayuda'),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateAyudaRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateAyudaCommand(
            id:              $id,
            numAyuda:        $request->input('num_ayuda'),
            idUs:            $request->has('id_us') ? $request->integer('id_us') : null,
            gestion:         $request->input('gestion'),
            montoPagado:     $request->input('monto_pagado'),
            nroRecibo:       $request->input('nro_recibo'),
            fechaRecibo:     $request->input('fecha_recibo'),
            observacionPago: $request->input('observacion_pago'),
            idCategoriatipo: $request->input('id_categoriatipoayuda'),
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteAyudaCommand($id));
        return response()->json(null, 204);
    }
}
