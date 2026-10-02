<?php

namespace App\Http\Controllers\Api;

use App\Application\CartaModelos\Commands\CreateCartaModeloCommand;
use App\Application\CartaModelos\Commands\DeleteCartaModeloCommand;
use App\Application\CartaModelos\Commands\UpdateCartaModeloCommand;
use App\Application\CartaModelos\Handlers\CreateCartaModeloHandler;
use App\Application\CartaModelos\Handlers\DeleteCartaModeloHandler;
use App\Application\CartaModelos\Handlers\UpdateCartaModeloHandler;
use App\Application\CartaModelos\Queries\GetCartaModeloByIdQuery;
use App\Application\CartaModelos\Queries\GetCartaModelosQuery;
use App\Application\CartaModelos\QueryHandlers\GetCartaModeloByIdQueryHandler;
use App\Application\CartaModelos\QueryHandlers\GetCartaModelosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CartaModelos\StoreCartaModeloRequest;
use App\Http\Requests\CartaModelos\UpdateCartaModeloRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartaModeloController extends Controller
{
    public function __construct(
        private readonly GetCartaModelosQueryHandler    $getCartaModelosHandler,
        private readonly GetCartaModeloByIdQueryHandler $getByIdHandler,
        private readonly CreateCartaModeloHandler       $createHandler,
        private readonly UpdateCartaModeloHandler       $updateHandler,
        private readonly DeleteCartaModeloHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->integer('pageIndex', 1),
            'pageSize'  => $request->integer('pageSize', 15),
        ]);

        return response()->json(
            $this->getCartaModelosHandler->handle(new GetCartaModelosQuery(
                pagination:   $pagination,
                query:        $request->input('query'),
                conInactivos: $request->boolean('conInactivos'),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getByIdHandler->handle(new GetCartaModeloByIdQuery($id))
        );
    }

    public function store(StoreCartaModeloRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCartaModeloCommand(
            idCartamod:                $request->integer('id_cartamod'),
            idUsReg:                   $request->integer('id_us_reg'),
            numCartamod:               $request->input('num_cartamod'),
            nombremodelo:              $request->input('nombremodelo'),
            textocarta:                $request->input('textocarta'),
            textocarta1:               $request->input('textocarta1'),
            textocarta3:               $request->input('textocarta3'),
            textoCarta:                $request->input('texto_carta'),
            usarEncabezadoPieEstandar: $request->boolean('usar_encabezado_pie_estandar'),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateCartaModeloRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateCartaModeloCommand(
            id:                        $id,
            numCartamod:               $request->input('num_cartamod'),
            nombremodelo:              $request->input('nombremodelo'),
            textocarta:                $request->input('textocarta'),
            textocarta1:               $request->input('textocarta1'),
            textocarta3:               $request->input('textocarta3'),
            textoCarta:                $request->input('texto_carta'),
            usarEncabezadoPieEstandar: $request->has('usar_encabezado_pie_estandar')
                                           ? $request->boolean('usar_encabezado_pie_estandar')
                                           : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteCartaModeloCommand($id));
        return response()->json(null, 204);
    }
}
