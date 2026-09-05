<?php

namespace App\Http\Controllers\Api;

use App\Application\Asesores\Commands\CreateAsesorCommand;
use App\Application\Asesores\Commands\DeleteAsesorCommand;
use App\Application\Asesores\Commands\UpdateAsesorCommand;
use App\Application\Asesores\Handlers\CreateAsesorHandler;
use App\Application\Asesores\Handlers\DeleteAsesorHandler;
use App\Application\Asesores\Handlers\UpdateAsesorHandler;
use App\Application\Asesores\Queries\GetAsesorByIdQuery;
use App\Application\Asesores\Queries\GetAsesoresQuery;
use App\Application\Asesores\QueryHandlers\GetAsesorByIdQueryHandler;
use App\Application\Asesores\QueryHandlers\GetAsesoresQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Asesores\AsignarAsesorRequest;
use App\Http\Requests\Asesores\StoreAsesorRequest;
use App\Http\Requests\Asesores\UpdateAsesorRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AsesorController extends Controller
{
    public function __construct(
        private readonly GetAsesoresQueryHandler $listHandler,
        private readonly GetAsesorByIdQueryHandler $showHandler,
        private readonly CreateAsesorHandler $createHandler,
        private readonly UpdateAsesorHandler $updateHandler,
        private readonly DeleteAsesorHandler $deleteHandler,
    ) {}

    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        $activo = $request->has('activo') ? (bool) $request->get('activo') : null;

        return response()->json($this->listHandler->handle(new GetAsesoresQuery(
            pageIndex: (int) $request->get('pageIndex', 1),
            pageSize:  (int) $request->get('pageSize', 15),
            query:     $request->input('query'),
            activo:    $activo,
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetAsesorByIdQuery($id)));
    }

    public function store(StoreAsesorRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateAsesorCommand(
            nombre:       $request->string('nombre')->toString(),
            telefono:     $request->string('telefono')->toString(),
            email:        $request->input('email'),
            especialidad: $request->input('especialidad'),
            disponible:   $request->boolean('disponible', true),
            activo:       $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateAsesorRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateAsesorCommand(
            id:           $id,
            nombre:       $request->input('nombre'),
            telefono:     $request->input('telefono'),
            email:        $request->input('email'),
            especialidad: $request->input('especialidad'),
            disponible:   $request->has('disponible') ? $request->boolean('disponible') : null,
            activo:       $request->has('activo') ? $request->boolean('activo') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteAsesorCommand($id));

        return response()->json(null, 204);
    }

    public function asignar(AsignarAsesorRequest $request, int $conversacionId): JsonResponse
    {
        $asesorId = $request->integer('asesor_id');

        DB::table('whatsapp_conversaciones')
            ->where('id', $conversacionId)
            ->update(['asesor_id' => $asesorId, 'updated_at' => now()]);

        return response()->json($this->showHandler->handle(new GetAsesorByIdQuery($asesorId)));
    }
}
