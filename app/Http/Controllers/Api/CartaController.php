<?php

namespace App\Http\Controllers\Api;

use App\Application\Cartas\Commands\CreateCartaCommand;
use App\Application\Cartas\Commands\DeleteCartaCommand;
use App\Application\Cartas\Commands\UpdateCartaCommand;
use App\Application\Cartas\Handlers\CreateCartaHandler;
use App\Application\Cartas\Handlers\DeleteCartaHandler;
use App\Application\Cartas\Handlers\UpdateCartaHandler;
use App\Application\Cartas\Queries\GetCartaByIdQuery;
use App\Application\Cartas\Queries\GetCartasQuery;
use App\Application\Cartas\QueryHandlers\GetCartaByIdQueryHandler;
use App\Application\Cartas\QueryHandlers\GetCartasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cartas\StoreCartaRequest;
use App\Http\Requests\Cartas\UpdateCartaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartaController extends Controller
{
    public function __construct(
        private readonly GetCartasQueryHandler    $getCartasHandler,
        private readonly GetCartaByIdQueryHandler $getByIdHandler,
        private readonly CreateCartaHandler       $createHandler,
        private readonly UpdateCartaHandler       $updateHandler,
        private readonly DeleteCartaHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->integer('pageIndex', 1),
            'pageSize'  => $request->integer('pageSize', 15),
        ]);

        return response()->json(
            $this->getCartasHandler->handle(new GetCartasQuery(
                pagination:   $pagination,
                idUs:         $request->integer('idUs') ?: null,
                idPlan:       $request->integer('idPlan') ?: null,
                conInactivos: $request->boolean('conInactivos'),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getByIdHandler->handle(new GetCartaByIdQuery($id))
        );
    }

    public function store(StoreCartaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCartaCommand(
            idCarta:      $request->integer('id_carta'),
            idUsReg:      $request->integer('id_us_reg'),
            numCarta:     $request->input('num_carta'),
            idUs:         $request->integer('id_us'),
            idPlan:       $request->integer('id_plan'),
            nombresenor:  $request->input('nombresenor'),
            nombretitulo: $request->input('nombretitulo'),
            textocarta1:  $request->input('textocarta1'),
            textocarta2:  $request->input('textocarta2'),
            textocarta3:  $request->input('textocarta3'),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateCartaRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateCartaCommand(
            id:           $id,
            numCarta:     $request->input('num_carta'),
            idUs:         $request->has('id_us') ? $request->integer('id_us') : null,
            idPlan:       $request->has('id_plan') ? $request->integer('id_plan') : null,
            nombresenor:  $request->input('nombresenor'),
            nombretitulo: $request->input('nombretitulo'),
            textocarta1:  $request->input('textocarta1'),
            textocarta2:  $request->input('textocarta2'),
            textocarta3:  $request->input('textocarta3'),
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteCartaCommand($id));
        return response()->json(null, 204);
    }
}
