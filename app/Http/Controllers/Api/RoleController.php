<?php

namespace App\Http\Controllers\Api;

use App\Domain\Usuarios\Contracts\RoleRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Roles\StoreRoleRequest;
use App\Http\Requests\Roles\UpdateRoleRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    private const NOMBRES_RESERVADOS = ['vendedor', 'admin'];

    private function esNombreReservado(?string $nombre): bool
    {
        if ($nombre === null) {
            return false;
        }

        return in_array(strtolower($nombre), self::NOMBRES_RESERVADOS, true);
    }

    public function __construct(
        private readonly RoleRepositoryInterface $repository
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->repository->all());
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->repository->findById($id));
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $dto = $this->repository->create([
            'nombre'                 => $request->nombre,
            'descripcion'            => $request->descripcion,
            'permisos'               => $request->permisos ?? [],
            'activo'                 => $request->boolean('activo', true),
            'restringido_a_vendedor' => $request->boolean('restringido_a_vendedor', false),
        ]);

        return response()->json($dto, 201);
    }

    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        $dto = $this->repository->update($id, [
            'nombre'                 => $request->nombre,
            'descripcion'            => $request->descripcion,
            'permisos'               => $request->permisos ?? [],
            'activo'                 => $request->has('activo') ? $request->boolean('activo') : null,
            'restringido_a_vendedor' => $request->has('restringido_a_vendedor') ? $request->boolean('restringido_a_vendedor') : null,
        ]);

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $nombre = DB::table('roles')->where('id', $id)->value('nombre');
        if ($this->esNombreReservado($nombre)) {
            return response()->json([
                'error' => "El rol \"{$nombre}\" es reservado por el sistema y no puede eliminarse.",
            ], 422);
        }

        $this->repository->delete($id);

        return response()->json(null, 204);
    }
}
