<?php

namespace App\Http\Controllers\Api;

use App\Application\Acreditaciones\Commands\CreateAcreditacionCommand;
use App\Application\Acreditaciones\Commands\DeleteAcreditacionCommand;
use App\Application\Acreditaciones\Commands\UpdateAcreditacionCommand;
use App\Application\Acreditaciones\Handlers\CreateAcreditacionHandler;
use App\Application\Acreditaciones\Handlers\DeleteAcreditacionHandler;
use App\Application\Acreditaciones\Handlers\UpdateAcreditacionHandler;
use App\Application\Acreditaciones\Queries\GetAcreditacionByIdQuery;
use App\Application\Acreditaciones\Queries\GetAcreditacionesQuery;
use App\Application\Acreditaciones\QueryHandlers\GetAcreditacionByIdQueryHandler;
use App\Application\Acreditaciones\QueryHandlers\GetAcreditacionesQueryHandler;
use App\Domain\Acreditaciones\Exceptions\AcreditacionNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Acreditaciones\StoreAcreditacionRequest;
use App\Http\Requests\Acreditaciones\UpdateAcreditacionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcreditacionController extends Controller
{
    public function __construct(
        private readonly GetAcreditacionesQueryHandler  $getHandler,
        private readonly GetAcreditacionByIdQueryHandler $getByIdHandler,
        private readonly CreateAcreditacionHandler      $createHandler,
        private readonly UpdateAcreditacionHandler      $updateHandler,
        private readonly DeleteAcreditacionHandler      $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $resultado = $this->getHandler->handle(new GetAcreditacionesQuery(
            page:     (int) $request->get('pageIndex', 1),
            pageSize: (int) $request->get('pageSize', 20),
            query:    $request->filled('query') ? $request->get('query') : null,
            activo:   $request->has('activo') ? $request->boolean('activo') : null,
        ));

        return response()->json($resultado);
    }

    public function show(int $id): JsonResponse
    {
        try {
            return response()->json(
                $this->getByIdHandler->handle(new GetAcreditacionByIdQuery($id))
            );
        } catch (AcreditacionNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function store(StoreAcreditacionRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateAcreditacionCommand(
            nombre:            $request->nombre,
            entidad_otorgante: $request->entidad_otorgante,
            tipo:              $request->tipo,
            descripcion:       $request->descripcion,
            logo_url:          $request->logo_url,
            logo_alt:          $request->logo_alt,
            url_verificacion:  $request->url_verificacion,
            fecha_obtencion:   $request->fecha_obtencion,
            fecha_vencimiento: $request->fecha_vencimiento,
            orden:             $request->integer('orden', 0),
            activo:            $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateAcreditacionRequest $request, int $id): JsonResponse
    {
        try {
            $dto = $this->updateHandler->handle(new UpdateAcreditacionCommand(
                id:                $id,
                nombre:            $request->nombre,
                entidad_otorgante: $request->entidad_otorgante,
                tipo:              $request->tipo,
                descripcion:       $request->descripcion,
                logo_url:          $request->logo_url,
                logo_alt:          $request->logo_alt,
                url_verificacion:  $request->url_verificacion,
                fecha_obtencion:   $request->fecha_obtencion,
                fecha_vencimiento: $request->fecha_vencimiento,
                orden:             $request->has('orden') ? $request->integer('orden') : null,
                activo:            $request->has('activo') ? $request->boolean('activo') : null,
            ));

            return response()->json($dto);
        } catch (AcreditacionNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->deleteHandler->handle(new DeleteAcreditacionCommand($id));

            return response()->json(null, 204);
        } catch (AcreditacionNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
