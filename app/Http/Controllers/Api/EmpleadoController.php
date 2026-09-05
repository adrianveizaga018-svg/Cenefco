<?php

namespace App\Http\Controllers\Api;

use App\Application\Empleados\Commands\CreateEmpleadoCommand;
use App\Application\Empleados\Commands\DeleteEmpleadoCommand;
use App\Application\Empleados\Commands\UpdateEmpleadoCommand;
use App\Application\Empleados\Handlers\CreateEmpleadoHandler;
use App\Application\Empleados\Handlers\DeleteEmpleadoHandler;
use App\Application\Empleados\Handlers\UpdateEmpleadoHandler;
use App\Application\Empleados\Queries\GetEmpleadoByIdQuery;
use App\Application\Empleados\Queries\GetEmpleadosActivosQuery;
use App\Application\Empleados\Queries\GetEmpleadosQuery;
use App\Application\Empleados\QueryHandlers\GetEmpleadoByIdQueryHandler;
use App\Application\Empleados\QueryHandlers\GetEmpleadosActivosQueryHandler;
use App\Application\Empleados\QueryHandlers\GetEmpleadosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Empleados\StoreEmpleadoRequest;
use App\Http\Requests\Empleados\UpdateEmpleadoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Empleados')]
class EmpleadoController extends Controller
{
    public function __construct(
        private readonly GetEmpleadosQueryHandler        $getListHandler,
        private readonly GetEmpleadoByIdQueryHandler     $getByIdHandler,
        private readonly GetEmpleadosActivosQueryHandler $getActivosHandler,
        private readonly CreateEmpleadoHandler            $createHandler,
        private readonly UpdateEmpleadoHandler            $updateHandler,
        private readonly DeleteEmpleadoHandler            $deleteHandler,
    ) {}

    #[OA\Get(path: '/api/v1/empleados', summary: 'Listar empleados', tags: ['Empleados'])]
    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
            'query'     => $request->get('query', ''),
        ], 'nombre_completo');

        return response()->json($this->getListHandler->handle(new GetEmpleadosQuery($pagination)));
    }

    #[OA\Get(path: '/api/v1/empleados/activos', summary: 'Listar empleados activos (sin paginación)', tags: ['Empleados'])]
    public function activos(): JsonResponse
    {
        return response()->json($this->getActivosHandler->handle(new GetEmpleadosActivosQuery()));
    }

    #[OA\Get(path: '/api/v1/empleados/{id}', summary: 'Obtener empleado por ID', tags: ['Empleados'])]
    public function show(int $empleado): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetEmpleadoByIdQuery($empleado)));
    }

    #[OA\Post(path: '/api/v1/empleados', summary: 'Crear empleado', tags: ['Empleados'])]
    public function store(StoreEmpleadoRequest $request): JsonResponse
    {
        $carnetPdf = $request->file('carnet')->store('empleados-carnets', 'public');

        $dto = $this->createHandler->handle(new CreateEmpleadoCommand(
            nombre_completo:      $request->nombre_completo,
            cargo:                $request->cargo,
            sueldo_mensual:       (float) $request->sueldo_mensual,
            ci:                   $request->ci,
            carnet_pdf:           $carnetPdf,
            correo:               $request->correo,
            celular_personal:     $request->celular_personal,
            celular_corporativo:  $request->celular_corporativo,
            direccion:            $request->direccion,
            fecha_ingreso:        $request->fecha_ingreso,
            activo:               $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    #[OA\Put(path: '/api/v1/empleados/{id}', summary: 'Actualizar empleado', tags: ['Empleados'])]
    public function update(UpdateEmpleadoRequest $request, int $empleado): JsonResponse
    {
        $carnetPdf = $request->hasFile('carnet')
            ? $request->file('carnet')->store('empleados-carnets', 'public')
            : null;

        return response()->json($this->updateHandler->handle(new UpdateEmpleadoCommand(
            id:                   $empleado,
            nombre_completo:      $request->nombre_completo,
            cargo:                $request->cargo,
            sueldo_mensual:       $request->has('sueldo_mensual') ? (float) $request->sueldo_mensual : null,
            ci:                   $request->ci,
            carnet_pdf:           $carnetPdf,
            correo:               $request->correo,
            celular_personal:     $request->celular_personal,
            celular_corporativo:  $request->celular_corporativo,
            direccion:            $request->direccion,
            fecha_ingreso:        $request->fecha_ingreso,
            activo:               $request->has('activo') ? $request->boolean('activo') : null,
        )));
    }

    #[OA\Delete(path: '/api/v1/empleados/{id}', summary: 'Eliminar empleado', tags: ['Empleados'])]
    public function destroy(int $empleado): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteEmpleadoCommand($empleado));

        return response()->json(null, 204);
    }
}
