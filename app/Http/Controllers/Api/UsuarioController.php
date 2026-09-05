<?php

namespace App\Http\Controllers\Api;

use App\Application\Usuarios\Commands\CreateUsuarioCommand;
use App\Application\Usuarios\Commands\DeleteUsuarioCommand;
use App\Application\Usuarios\Commands\UpdateUsuarioCommand;
use App\Application\Usuarios\Handlers\CreateUsuarioHandler;
use App\Application\Usuarios\Handlers\DeleteUsuarioHandler;
use App\Application\Usuarios\Handlers\UpdateUsuarioHandler;
use App\Application\Usuarios\Queries\GetUsuarioByIdQuery;
use App\Application\Usuarios\Queries\GetUsuariosQuery;
use App\Application\Usuarios\QueryHandlers\GetUsuarioByIdQueryHandler;
use App\Application\Usuarios\QueryHandlers\GetUsuariosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Usuarios\StoreUsuarioRequest;
use App\Http\Requests\Usuarios\UpdateUsuarioRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function __construct(
        private readonly GetUsuariosQueryHandler $getUsuariosHandler,
        private readonly GetUsuarioByIdQueryHandler $getUsuarioByIdHandler,
        private readonly CreateUsuarioHandler $createHandler,
        private readonly UpdateUsuarioHandler $updateHandler,
        private readonly DeleteUsuarioHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'created_at'),
            'sortOrder' => $request->input('sort.order', 'desc'),
        ]);

        $filters = array_filter([
            'origen' => $request->get('origen'),
        ]);

        return response()->json(
            $this->getUsuariosHandler->handle(new GetUsuariosQuery($pagination, $filters))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getUsuarioByIdHandler->handle(new GetUsuarioByIdQuery($id))
        );
    }

    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateUsuarioCommand(
            nombre:   $request->nombre,
            apellido: $request->apellido,
            email:    $request->email,
            password: $request->password,
            tipo:     $request->tipo ?? 'participante',
            rolId:    $request->rol_id,
            activo:   $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateUsuarioRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateUsuarioCommand(
            id:       $id,
            nombre:   $request->nombre,
            apellido: $request->apellido,
            email:    $request->email,
            password: $request->filled('password') ? $request->password : null,
            tipo:     $request->tipo,
            rolId:    $request->rol_id,
            activo:   $request->has('activo') ? $request->boolean('activo') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteUsuarioCommand($id));

        return response()->json(null, 204);
    }
}
