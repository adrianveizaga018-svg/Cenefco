<?php

namespace App\Http\Controllers\Api;

use App\Application\Pagos\Commands\CreateDevolucionCommand;
use App\Application\Pagos\Commands\ResolverDevolucionCommand;
use App\Application\Pagos\Handlers\CreateDevolucionHandler;
use App\Application\Pagos\Handlers\ResolverDevolucionHandler;
use App\Application\Pagos\Queries\GetDevolucionesQuery;
use App\Application\Pagos\QueryHandlers\GetDevolucionesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pagos\ResolverDevolucionRequest;
use App\Http\Requests\Pagos\StoreDevolucionRequest;
use Illuminate\Http\JsonResponse;

class DevolucionController extends Controller
{
    public function __construct(
        private readonly GetDevolucionesQueryHandler $getDevolucionesHandler,
        private readonly CreateDevolucionHandler     $createHandler,
        private readonly ResolverDevolucionHandler   $resolverHandler,
    ) {}

    public function index(int $idIns): JsonResponse
    {
        return response()->json(
            $this->getDevolucionesHandler->handle(new GetDevolucionesQuery($idIns))
        );
    }

    public function store(StoreDevolucionRequest $request, int $idIns): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateDevolucionCommand(
            idIns:        $idIns,
            monto:        (float) $request->monto,
            motivo:       $request->motivo,
            documentoUrl: $request->documento_url,
        ));

        return response()->json($dto, 201);
    }

    public function update(ResolverDevolucionRequest $request, int $id): JsonResponse
    {
        return response()->json(
            $this->resolverHandler->handle(new ResolverDevolucionCommand(
                id:            $id,
                estado:        $request->estado,
                notaRespuesta: $request->nota_respuesta,
            ))
        );
    }
}
