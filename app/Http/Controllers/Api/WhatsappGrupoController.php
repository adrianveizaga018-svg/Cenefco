<?php

namespace App\Http\Controllers\Api;

use App\Application\WhatsappGrupos\Commands\CreateWhatsappGrupoCommand;
use App\Application\WhatsappGrupos\Commands\DeleteWhatsappGrupoCommand;
use App\Application\WhatsappGrupos\Commands\UpdateWhatsappGrupoCommand;
use App\Application\WhatsappGrupos\Handlers\CreateWhatsappGrupoHandler;
use App\Application\WhatsappGrupos\Handlers\DeleteWhatsappGrupoHandler;
use App\Application\WhatsappGrupos\Handlers\UpdateWhatsappGrupoHandler;
use App\Application\WhatsappGrupos\Queries\GetWhatsappGrupoByIdQuery;
use App\Application\WhatsappGrupos\Queries\GetWhatsappGruposQuery;
use App\Application\WhatsappGrupos\QueryHandlers\GetWhatsappGrupoByIdQueryHandler;
use App\Application\WhatsappGrupos\QueryHandlers\GetWhatsappGruposQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\WhatsappGrupos\StoreWhatsappGrupoRequest;
use App\Http\Requests\WhatsappGrupos\UpdateWhatsappGrupoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsappGrupoController extends Controller
{
    public function __construct(
        private readonly GetWhatsappGruposQueryHandler $getGruposHandler,
        private readonly GetWhatsappGrupoByIdQueryHandler $getByIdHandler,
        private readonly CreateWhatsappGrupoHandler $createHandler,
        private readonly UpdateWhatsappGrupoHandler $updateHandler,
        private readonly DeleteWhatsappGrupoHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'orden'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getGruposHandler->handle(
                new GetWhatsappGruposQuery($pagination, $request->boolean('soloActivos', false))
            )
        );
    }

    public function store(StoreWhatsappGrupoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateWhatsappGrupoCommand(
            imparte_id:              (int) $request->imparte_id,
            nombre:                  $request->nombre,
            enlace_invitacion:       $request->enlace_invitacion,
            capacidad_maxima:        $request->capacidad_maxima ? (int) $request->capacidad_maxima : null,
            miembros_actuales:       $request->integer('miembros_actuales', 0),
            descripcion:             $request->descripcion,
            activo:                  $request->boolean('activo', true),
            orden:                   $request->integer('orden', 0),
            fecha_expiracion_enlace: $request->fecha_expiracion_enlace,
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetWhatsappGrupoByIdQuery($id)));
    }

    public function update(UpdateWhatsappGrupoRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateWhatsappGrupoCommand(
            id:                      $id,
            imparte_id:              $request->has('imparte_id') ? (int) $request->imparte_id : null,
            nombre:                  $request->nombre,
            enlace_invitacion:       $request->enlace_invitacion,
            capacidad_maxima:        $request->has('capacidad_maxima') ? (int) $request->capacidad_maxima : null,
            miembros_actuales:       $request->has('miembros_actuales') ? $request->integer('miembros_actuales') : null,
            descripcion:             $request->descripcion,
            activo:                  $request->has('activo') ? $request->boolean('activo') : null,
            orden:                   $request->has('orden') ? $request->integer('orden') : null,
            fecha_expiracion_enlace: $request->fecha_expiracion_enlace,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteWhatsappGrupoCommand($id));

        return response()->json(null, 204);
    }
}
