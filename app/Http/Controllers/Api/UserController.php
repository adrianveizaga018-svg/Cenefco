<?php

namespace App\Http\Controllers\Api;

use App\Application\Usuarios\Commands\RegisterUserCommand;
use App\Application\Usuarios\Commands\UpdateUserCommand;
use App\Application\Usuarios\Handlers\DeleteUserHandler;
use App\Application\Usuarios\Handlers\GetUsersQueryHandler;
use App\Application\Usuarios\Handlers\RegisterUserHandler;
use App\Application\Usuarios\Handlers\UpdateUserHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Usuarios\StoreUserRequest;
use App\Http\Requests\Usuarios\UpdateUserRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly GetUsersQueryHandler $queryHandler,
        private readonly RegisterUserHandler $createHandler,
        private readonly UpdateUserHandler $updateHandler,
        private readonly DeleteUserHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->queryHandler->handle(new PaginationDTO(
            pageIndex: (int) $request->get('pageIndex', 1),
            pageSize: (int) $request->get('pageSize', 15),
            query: $request->get('query'),
        ));

        return response()->json($result);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $userDTO = $this->createHandler->handle(new RegisterUserCommand(
            name: $request->name,
            email: $request->email,
            password: $request->password,
            roleId: $request->role_id,
        ));

        return response()->json($userDTO, 201);
    }

    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $userDTO = $this->updateHandler->handle(new UpdateUserCommand(
            id: $id,
            name: $request->name,
            email: $request->email,
            roleId: $request->role_id,
        ));

        return response()->json($userDTO);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle($id);

        return response()->json(['message' => 'Usuario eliminado.']);
    }
}
