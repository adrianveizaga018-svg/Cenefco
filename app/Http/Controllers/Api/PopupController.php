<?php

namespace App\Http\Controllers\Api;

use App\Application\Popups\Commands\CreatePopupCommand;
use App\Application\Popups\Commands\DeletePopupCommand;
use App\Application\Popups\Commands\UpdatePopupCommand;
use App\Application\Popups\Handlers\CreatePopupHandler;
use App\Application\Popups\Handlers\DeletePopupHandler;
use App\Application\Popups\Handlers\UpdatePopupHandler;
use App\Application\Popups\Queries\GetPopupByIdQuery;
use App\Application\Popups\Queries\GetPopupsQuery;
use App\Application\Popups\QueryHandlers\GetPopupByIdQueryHandler;
use App\Application\Popups\QueryHandlers\GetPopupsQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Popups\StorePopupRequest;
use App\Http\Requests\Popups\UpdatePopupRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PopupController extends Controller
{
    public function __construct(
        private readonly GetPopupsQueryHandler $getPopupsHandler,
        private readonly GetPopupByIdQueryHandler $getByIdHandler,
        private readonly CreatePopupHandler $createHandler,
        private readonly UpdatePopupHandler $updateHandler,
        private readonly DeletePopupHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'id'),
            'sortOrder' => $request->input('sort.order', 'desc'),
        ]);

        return response()->json($this->getPopupsHandler->handle(new GetPopupsQuery($pagination)));
    }

    public function store(StorePopupRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreatePopupCommand(
            titulo:                  $request->titulo,
            contenido:               $request->contenido,
            imagen_url:              $request->imagen_url,
            enlace_url:              $request->enlace_url,
            enlace_texto:            $request->enlace_texto,
            posicion:                $request->input('posicion', 'center'),
            delay_segundos:          $request->integer('delay_segundos', 3),
            mostrar_una_vez_sesion:  $request->boolean('mostrar_una_vez_sesion', true),
            mostrar_una_vez_siempre: $request->boolean('mostrar_una_vez_siempre', false),
            paginas_mostrar:         $request->paginas_mostrar,
            activo:                  $request->boolean('activo', false),
            fecha_inicio:            $request->fecha_inicio,
            fecha_fin:               $request->fecha_fin,
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetPopupByIdQuery($id)));
    }

    public function update(UpdatePopupRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdatePopupCommand(
            id:                      $id,
            titulo:                  $request->titulo,
            contenido:               $request->contenido,
            imagen_url:              $request->imagen_url,
            enlace_url:              $request->enlace_url,
            enlace_texto:            $request->enlace_texto,
            posicion:                $request->posicion,
            delay_segundos:          $request->has('delay_segundos') ? $request->integer('delay_segundos') : null,
            mostrar_una_vez_sesion:  $request->has('mostrar_una_vez_sesion') ? $request->boolean('mostrar_una_vez_sesion') : null,
            mostrar_una_vez_siempre: $request->has('mostrar_una_vez_siempre') ? $request->boolean('mostrar_una_vez_siempre') : null,
            paginas_mostrar:         $request->paginas_mostrar,
            activo:                  $request->has('activo') ? $request->boolean('activo') : null,
            fecha_inicio:            $request->fecha_inicio,
            fecha_fin:               $request->fecha_fin,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeletePopupCommand($id));

        return response()->json(null, 204);
    }
}
