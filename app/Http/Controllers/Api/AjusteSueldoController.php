<?php

namespace App\Http\Controllers\Api;

use App\Application\AjustesSueldo\Commands\CreateAjusteSueldoCommand;
use App\Application\AjustesSueldo\Commands\DeleteAjusteSueldoCommand;
use App\Application\AjustesSueldo\Handlers\CreateAjusteSueldoHandler;
use App\Application\AjustesSueldo\Handlers\DeleteAjusteSueldoHandler;
use App\Application\AjustesSueldo\Queries\GetAjustesSueldoQuery;
use App\Application\AjustesSueldo\QueryHandlers\GetAjustesSueldoQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\AjustesSueldo\StoreAjusteSueldoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AjusteSueldoController extends Controller
{
    public function __construct(
        private readonly GetAjustesSueldoQueryHandler $getAjustesHandler,
        private readonly CreateAjusteSueldoHandler    $createHandler,
        private readonly DeleteAjusteSueldoHandler    $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
        ]);

        return response()->json(
            $this->getAjustesHandler->handle(new GetAjustesSueldoQuery(
                pagination:  $pagination,
                empleadoId:  $request->filled('empleado_id') ? $request->integer('empleado_id') : null,
                anio:        $request->filled('anio') ? $request->integer('anio') : null,
                mes:         $request->filled('mes') ? $request->integer('mes') : null,
                aplicado:    $request->has('aplicado') ? $request->boolean('aplicado') : null,
            ))
        );
    }

    public function store(StoreAjusteSueldoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateAjusteSueldoCommand(
            empleadoId:    $request->integer('empleado_id'),
            anio:          $request->integer('anio'),
            mes:           $request->integer('mes'),
            tipo:          $request->string('tipo')->toString(),
            monto:         (float) $request->monto,
            motivo:        $request->string('motivo')->toString(),
            registradoPor: auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteAjusteSueldoCommand($id));

        return response()->json(null, 204);
    }
}
