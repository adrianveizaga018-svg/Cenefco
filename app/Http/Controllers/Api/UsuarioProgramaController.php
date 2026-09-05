<?php

namespace App\Http\Controllers\Api;

use App\Application\UsuariosPrograma\Commands\CreateUsuarioProgramaCommand;
use App\Application\UsuariosPrograma\Commands\DeleteUsuarioProgramaCommand;
use App\Application\UsuariosPrograma\Commands\UpdateUsuarioProgramaCommand;
use App\Application\UsuariosPrograma\Handlers\CreateUsuarioProgramaHandler;
use App\Application\UsuariosPrograma\Handlers\DeleteUsuarioProgramaHandler;
use App\Application\UsuariosPrograma\Handlers\UpdateUsuarioProgramaHandler;
use App\Application\UsuariosPrograma\Queries\GetUsuarioProgramaByIdQuery;
use App\Application\UsuariosPrograma\Queries\GetUsuariosProgramaQuery;
use App\Application\UsuariosPrograma\QueryHandlers\GetUsuarioProgramaByIdQueryHandler;
use App\Application\UsuariosPrograma\QueryHandlers\GetUsuariosProgramaQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\UsuariosPrograma\StoreUsuarioProgramaRequest;
use App\Http\Requests\UsuariosPrograma\UpdateUsuarioProgramaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsuarioProgramaController extends Controller
{
    public function __construct(
        private readonly GetUsuariosProgramaQueryHandler    $listHandler,
        private readonly GetUsuarioProgramaByIdQueryHandler $showHandler,
        private readonly CreateUsuarioProgramaHandler       $createHandler,
        private readonly UpdateUsuarioProgramaHandler       $updateHandler,
        private readonly DeleteUsuarioProgramaHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetUsuariosProgramaQuery(
                pagination:      $pagination,
                idUs:            $request->filled('id_us') ? (int) $request->get('id_us') : null,
                idPrograma:      $request->filled('id_programa') ? (int) $request->get('id_programa') : null,
                idTipoPrograma:  $request->filled('id_tipoprograma') ? (int) $request->get('id_tipoprograma') : null,
                conInactivos:    $request->boolean('conInactivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetUsuarioProgramaByIdQuery($id))
        );
    }

    public function store(StoreUsuarioProgramaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateUsuarioProgramaCommand(
            id_usuarioprograma:  $request->integer('id_usuarioprograma'),
            id_us:               $request->integer('id_us'),
            id_us_reg:           $request->integer('id_us_reg', 0),
            num_usuarioprograma: $request->integer('num_usuarioprograma', 0),
            id_programa:         $request->filled('id_programa') ? $request->integer('id_programa') : null,
            id_tipoprograma:     $request->filled('id_tipoprograma') ? $request->integer('id_tipoprograma') : null,
            estado:              $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateUsuarioProgramaRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateUsuarioProgramaCommand(
            id:             $id,
            id_programa:    $request->filled('id_programa') ? $request->integer('id_programa') : null,
            id_tipoprograma: $request->filled('id_tipoprograma') ? $request->integer('id_tipoprograma') : null,
            estado:         $request->filled('estado') ? $request->integer('estado') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteUsuarioProgramaCommand($id));

        return response()->json(null, 204);
    }
}
