<?php

namespace App\Http\Controllers\Api;

use App\Application\Resenas\Commands\CreateResenaCommand;
use App\Application\Resenas\Commands\DeleteResenaCommand;
use App\Application\Resenas\Commands\UpdateResenaCommand;
use App\Application\Resenas\Handlers\CreateResenaHandler;
use App\Application\Resenas\Handlers\DeleteResenaHandler;
use App\Application\Resenas\Handlers\UpdateResenaHandler;
use App\Application\Resenas\Queries\GetEstudiantesProgramaQuery;
use App\Application\Resenas\Queries\GetResenaByIdQuery;
use App\Application\Resenas\Queries\GetResenasQuery;
use App\Application\Resenas\QueryHandlers\GetEstudiantesProgramaQueryHandler;
use App\Application\Resenas\QueryHandlers\GetResenaByIdQueryHandler;
use App\Application\Resenas\QueryHandlers\GetResenasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resenas\StoreResenaRequest;
use App\Http\Requests\Resenas\UpdateResenaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResenaController extends Controller
{
    public function __construct(
        private readonly GetResenasQueryHandler             $getResenasHandler,
        private readonly GetResenaByIdQueryHandler          $getResenaByIdHandler,
        private readonly GetEstudiantesProgramaQueryHandler $getEstudiantesHandler,
        private readonly CreateResenaHandler                $createHandler,
        private readonly UpdateResenaHandler                $updateHandler,
        private readonly DeleteResenaHandler                $deleteHandler,
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

        return response()->json(
            $this->getResenasHandler->handle(new GetResenasQuery(
                pagination:  $pagination,
                estado:      $request->filled('estado') ? $request->get('estado') : null,
                programaId:  $request->filled('programa_id') ? (int) $request->get('programa_id') : null,
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getResenaByIdHandler->handle(new GetResenaByIdQuery($id))
        );
    }

    public function store(StoreResenaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateResenaCommand(
            programa_id:   $request->integer('programa_id'),
            usuario_id:    $request->filled('usuario_id') ? $request->integer('usuario_id') : null,
            nombre:        $request->string('nombre'),
            cargo_actual:  $request->input('cargo_actual'),
            foto_url:      $request->input('foto_url'),
            calificacion:  $request->integer('calificacion'),
            titulo_resena: $request->input('titulo_resena'),
            resena:        $request->string('resena'),
            destacada:     $request->boolean('destacada', false),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateResenaRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateResenaCommand(
            id:             $id,
            estado:         $request->input('estado'),
            verificado:     $request->filled('verificado') ? $request->boolean('verificado') : null,
            destacada:      $request->filled('destacada') ? $request->boolean('destacada') : null,
            motivo_rechazo: $request->input('motivo_rechazo'),
            foto_url:       $request->input('foto_url'),
            nombre:         $request->input('nombre'),
            cargo_actual:   $request->input('cargo_actual'),
            calificacion:   $request->filled('calificacion') ? $request->integer('calificacion') : null,
            titulo_resena:  $request->input('titulo_resena'),
            resena:         $request->input('resena'),
            usuario_id:     $request->filled('usuario_id') ? $request->integer('usuario_id') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteResenaCommand($id));
        return response()->json(null, 204);
    }

    public function estudiantesPrograma(int $programaId): JsonResponse
    {
        return response()->json(
            $this->getEstudiantesHandler->handle(new GetEstudiantesProgramaQuery($programaId))
        );
    }
}
