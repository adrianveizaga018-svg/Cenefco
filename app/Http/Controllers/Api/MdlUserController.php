<?php

namespace App\Http\Controllers\Api;

use App\Application\MdlUsers\Commands\CreateMdlUserCommand;
use App\Application\MdlUsers\Commands\DeleteMdlUserCommand;
use App\Application\MdlUsers\Commands\UpdateMdlUserCommand;
use App\Application\MdlUsers\Handlers\CreateMdlUserHandler;
use App\Application\MdlUsers\Handlers\DeleteMdlUserHandler;
use App\Application\MdlUsers\Handlers\UpdateMdlUserHandler;
use App\Application\MdlUsers\Queries\GetMdlUserByIdQuery;
use App\Application\MdlUsers\Queries\GetMdlUsersQuery;
use App\Application\MdlUsers\QueryHandlers\GetMdlUserByIdQueryHandler;
use App\Application\MdlUsers\QueryHandlers\GetMdlUsersQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\MdlUsers\StoreMdlUserRequest;
use App\Http\Requests\MdlUsers\UpdateMdlUserRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MdlUserController extends Controller
{
    public function __construct(
        private readonly GetMdlUsersQueryHandler $listHandler,
        private readonly GetMdlUserByIdQueryHandler $showHandler,
        private readonly CreateMdlUserHandler $createHandler,
        private readonly UpdateMdlUserHandler $updateHandler,
        private readonly DeleteMdlUserHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
        ]);

        return response()->json($this->listHandler->handle(new GetMdlUsersQuery(
            pagination:   $pagination,
            query:        $request->input('query'),
            idUsReg:      $request->integer('id_us_reg') ?: null,
            conInactivos: (bool) $request->get('conInactivos', false),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetMdlUserByIdQuery($id)));
    }

    public function store(StoreMdlUserRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateMdlUserCommand(
            id:            $request->integer('id'),
            numModusuario: $request->integer('num_modusuario') ?: null,
            nombreUsuario: $request->input('nombre_usuario'),
            nombre:        $request->input('nombre'),
            appaterno:     $request->input('appaterno'),
            apmaterno:     $request->input('apmaterno'),
            ci:            $request->input('ci'),
            expedido:      $request->integer('expedido') ?: null,
            telefono:      $request->input('telefono'),
            celular:       $request->input('celular'),
            email:         $request->input('email'),
            direccion:     $request->input('direccion'),
            ciudad:        $request->input('ciudad'),
            perModificar:  $request->integer('per_modificar') ?: null,
            idUsReg:       auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateMdlUserRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateMdlUserCommand(
            id:            $id,
            nombreUsuario: $request->input('nombre_usuario'),
            nombre:        $request->input('nombre'),
            appaterno:     $request->input('appaterno'),
            apmaterno:     $request->input('apmaterno'),
            ci:            $request->input('ci'),
            expedido:      $request->integer('expedido') ?: null,
            telefono:      $request->input('telefono'),
            celular:       $request->input('celular'),
            email:         $request->input('email'),
            direccion:     $request->input('direccion'),
            ciudad:        $request->input('ciudad'),
            estado:        $request->integer('estado') ?: null,
            perModificar:  $request->integer('per_modificar') ?: null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteMdlUserCommand($id));

        return response()->json(null, 204);
    }
}
