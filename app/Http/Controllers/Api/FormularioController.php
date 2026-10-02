<?php

namespace App\Http\Controllers\Api;

use App\Application\Formularios\Commands\CreateFormularioCommand;
use App\Application\Formularios\Commands\DeleteFormularioCommand;
use App\Application\Formularios\Commands\UpdateFormularioCommand;
use App\Application\Formularios\Handlers\CreateFormularioHandler;
use App\Application\Formularios\Handlers\DeleteFormularioHandler;
use App\Application\Formularios\Handlers\UpdateFormularioHandler;
use App\Application\Formularios\Queries\GetFormularioByIdQuery;
use App\Application\Formularios\Queries\GetFormularioBySlugQuery;
use App\Application\Formularios\Queries\GetFormulariosQuery;
use App\Application\Formularios\QueryHandlers\GetFormularioByIdQueryHandler;
use App\Application\Formularios\QueryHandlers\GetFormulariosActivosQueryHandler;
use App\Application\Formularios\QueryHandlers\GetFormularioBySlugQueryHandler;
use App\Application\Formularios\QueryHandlers\GetFormulariosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Formularios\StoreFormularioRequest;
use App\Http\Requests\Formularios\UpdateFormularioRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Formularios')]
class FormularioController extends Controller
{
    public function __construct(
        private readonly GetFormulariosQueryHandler        $getListHandler,
        private readonly GetFormularioByIdQueryHandler     $getByIdHandler,
        private readonly GetFormularioBySlugQueryHandler   $getBySlugHandler,
        private readonly GetFormulariosActivosQueryHandler $getActivosHandler,
        private readonly CreateFormularioHandler           $createHandler,
        private readonly UpdateFormularioHandler           $updateHandler,
        private readonly DeleteFormularioHandler           $deleteHandler,
    ) {}

    #[OA\Get(path: '/api/v1/formularios', summary: 'Listar formularios', tags: ['Formularios'])]
    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
            'query'     => $request->get('query', ''),
        ]);

        return response()->json($this->getListHandler->handle(new GetFormulariosQuery($pagination)));
    }

    #[OA\Get(path: '/api/v1/formularios/activos', summary: 'Listar formularios activos (sin paginación)', tags: ['Formularios'])]
    public function activos(): JsonResponse
    {
        return response()->json($this->getActivosHandler->handle());
    }

    #[OA\Get(path: '/api/v1/formularios/{id}', summary: 'Obtener formulario por ID', tags: ['Formularios'])]
    public function show(int $formulario): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetFormularioByIdQuery($formulario)));
    }

    #[OA\Get(path: '/api/v1/public/formularios/{slug}/publico', summary: 'Obtener formulario por slug (público)', tags: ['Portal'])]
    public function showBySlug(string $slug): JsonResponse
    {
        return response()->json($this->getBySlugHandler->handle(new GetFormularioBySlugQuery($slug)));
    }

    #[OA\Get(path: '/api/v1/public/formularios/id/{id}/publico', summary: 'Obtener formulario por ID (público)', tags: ['Portal'])]
    public function showByIdPublico(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetFormularioByIdQuery($id)));
    }

    #[OA\Post(path: '/api/v1/formularios', summary: 'Crear formulario', tags: ['Formularios'])]
    public function store(StoreFormularioRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateFormularioCommand(
            nombre:      $request->nombre,
            slug:        $request->slug,
            descripcion: $request->descripcion,
            campos:      $request->campos ?? [],
            activo:      $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    #[OA\Put(path: '/api/v1/formularios/{id}', summary: 'Actualizar formulario', tags: ['Formularios'])]
    public function update(UpdateFormularioRequest $request, int $formulario): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateFormularioCommand(
            id:          $formulario,
            nombre:      $request->nombre,
            slug:        $request->slug,
            descripcion: $request->descripcion,
            campos:      $request->has('campos') ? ($request->campos ?? []) : null,
            activo:      $request->has('activo') ? $request->boolean('activo') : null,
        )));
    }

    #[OA\Delete(path: '/api/v1/formularios/{id}', summary: 'Eliminar formulario', tags: ['Formularios'])]
    public function destroy(int $formulario): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteFormularioCommand($formulario));

        return response()->json(null, 204);
    }
}
