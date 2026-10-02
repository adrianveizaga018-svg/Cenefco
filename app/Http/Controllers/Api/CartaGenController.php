<?php

namespace App\Http\Controllers\Api;

use App\Application\CartaGens\Commands\CreateCartaGenCommand;
use App\Application\CartaGens\Commands\DeleteCartaGenCommand;
use App\Application\CartaGens\Commands\UpdateCartaGenCommand;
use App\Application\CartaGens\Handlers\CreateCartaGenHandler;
use App\Application\CartaGens\Handlers\DeleteCartaGenHandler;
use App\Application\CartaGens\Handlers\UpdateCartaGenHandler;
use App\Application\CartaGens\Queries\GetCartaGenByIdQuery;
use App\Application\CartaGens\Queries\GetCartaGensQuery;
use App\Application\CartaGens\QueryHandlers\GetCartaGenByIdQueryHandler;
use App\Application\CartaGens\QueryHandlers\GetCartaGensQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CartaGens\StoreCartaGenRequest;
use App\Http\Requests\CartaGens\UpdateCartaGenRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartaGenController extends Controller
{
    public function __construct(
        private readonly GetCartaGensQueryHandler    $getCartaGensHandler,
        private readonly GetCartaGenByIdQueryHandler $getByIdHandler,
        private readonly CreateCartaGenHandler       $createHandler,
        private readonly UpdateCartaGenHandler       $updateHandler,
        private readonly DeleteCartaGenHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->integer('pageIndex', 1),
            'pageSize'  => $request->integer('pageSize', 15),
        ]);

        return response()->json(
            $this->getCartaGensHandler->handle(new GetCartaGensQuery(
                pagination:   $pagination,
                idUs:         $request->integer('idUs') ?: null,
                idCartamod:   $request->integer('idCartamod') ?: null,
                conInactivos: $request->boolean('conInactivos'),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getByIdHandler->handle(new GetCartaGenByIdQuery($id))
        );
    }

    public function store(StoreCartaGenRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCartaGenCommand(
            idCartagen:                $request->integer('id_cartagen'),
            idUsReg:                   $request->integer('id_us_reg'),
            numCarta:                  $request->input('num_carta'),
            idUs:                      $request->integer('id_us'),
            idCartamod:                $request->integer('id_cartamod'),
            textocarta:                $request->input('textocarta'),
            textocarta1:               $request->input('textocarta1'),
            textocarta3:               $request->input('textocarta3'),
            usarEncabezadoPieEstandar: $request->boolean('usar_encabezado_pie_estandar'),
            cpNroContrato:             $request->input('cp_nro_contrato'),
            cpGestionContrato:         $request->input('cp_gestion_contrato'),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateCartaGenRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateCartaGenCommand(
            id:                        $id,
            numCarta:                  $request->input('num_carta'),
            idUs:                      $request->has('id_us') ? $request->integer('id_us') : null,
            idCartamod:                $request->has('id_cartamod') ? $request->integer('id_cartamod') : null,
            textocarta:                $request->input('textocarta'),
            textocarta1:               $request->input('textocarta1'),
            textocarta3:               $request->input('textocarta3'),
            usarEncabezadoPieEstandar: $request->has('usar_encabezado_pie_estandar')
                                           ? $request->boolean('usar_encabezado_pie_estandar')
                                           : null,
            cpNroContrato:             $request->input('cp_nro_contrato'),
            cpGestionContrato:         $request->input('cp_gestion_contrato'),
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteCartaGenCommand($id));
        return response()->json(null, 204);
    }
}
