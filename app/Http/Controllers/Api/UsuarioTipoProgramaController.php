<?php

namespace App\Http\Controllers\Api;

use App\Application\UsuariosTipoPrograma\Commands\CreateUsuarioTipoProgramaCommand;
use App\Application\UsuariosTipoPrograma\Commands\DeleteUsuarioTipoProgramaCommand;
use App\Application\UsuariosTipoPrograma\Commands\UpdateUsuarioTipoProgramaCommand;
use App\Application\UsuariosTipoPrograma\Handlers\CreateUsuarioTipoProgramaHandler;
use App\Application\UsuariosTipoPrograma\Handlers\DeleteUsuarioTipoProgramaHandler;
use App\Application\UsuariosTipoPrograma\Handlers\UpdateUsuarioTipoProgramaHandler;
use App\Application\UsuariosTipoPrograma\Queries\GetUsuarioTipoProgramaByIdQuery;
use App\Application\UsuariosTipoPrograma\Queries\GetUsuariosTipoProgramaQuery;
use App\Application\UsuariosTipoPrograma\QueryHandlers\GetUsuarioTipoProgramaByIdQueryHandler;
use App\Application\UsuariosTipoPrograma\QueryHandlers\GetUsuariosTipoProgramaQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\UsuariosTipoPrograma\StoreUsuarioTipoProgramaRequest;
use App\Http\Requests\UsuariosTipoPrograma\UpdateUsuarioTipoProgramaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsuarioTipoProgramaController extends Controller
{
    public function __construct(
        private readonly GetUsuariosTipoProgramaQueryHandler    $listHandler,
        private readonly GetUsuarioTipoProgramaByIdQueryHandler $showHandler,
        private readonly CreateUsuarioTipoProgramaHandler       $createHandler,
        private readonly UpdateUsuarioTipoProgramaHandler       $updateHandler,
        private readonly DeleteUsuarioTipoProgramaHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetUsuariosTipoProgramaQuery(
                pagination:     $pagination,
                idUs:           $request->filled('id_us') ? (int) $request->get('id_us') : null,
                idTipoPrograma: $request->filled('id_tipoprograma') ? (int) $request->get('id_tipoprograma') : null,
                conInactivos:   $request->boolean('conInactivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetUsuarioTipoProgramaByIdQuery($id))
        );
    }

    public function store(StoreUsuarioTipoProgramaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateUsuarioTipoProgramaCommand(
            id_usuariotipoprograma:  $request->integer('id_usuariotipoprograma'),
            id_us:                   $request->integer('id_us'),
            id_us_reg:               $request->integer('id_us_reg', 0),
            num_usuariotipoprograma: $request->integer('num_usuariotipoprograma', 0),
            id_tipoprograma:         $request->filled('id_tipoprograma') ? $request->integer('id_tipoprograma') : null,
            estado:                  $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateUsuarioTipoProgramaRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateUsuarioTipoProgramaCommand(
            id:             $id,
            id_tipoprograma: $request->filled('id_tipoprograma') ? $request->integer('id_tipoprograma') : null,
            estado:         $request->filled('estado') ? $request->integer('estado') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteUsuarioTipoProgramaCommand($id));

        return response()->json(null, 204);
    }
}
