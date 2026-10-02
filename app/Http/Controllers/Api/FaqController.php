<?php

namespace App\Http\Controllers\Api;

use App\Application\Faqs\Commands\CreateFaqCommand;
use App\Application\Faqs\Commands\DeleteFaqCommand;
use App\Application\Faqs\Commands\UpdateFaqCommand;
use App\Application\Faqs\Handlers\CreateFaqHandler;
use App\Application\Faqs\Handlers\DeleteFaqHandler;
use App\Application\Faqs\Handlers\UpdateFaqHandler;
use App\Application\Faqs\Queries\GetFaqByIdQuery;
use App\Application\Faqs\Queries\GetFaqsQuery;
use App\Application\Faqs\QueryHandlers\GetFaqByIdQueryHandler;
use App\Application\Faqs\QueryHandlers\GetFaqsQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Faqs\StoreFaqRequest;
use App\Http\Requests\Faqs\UpdateFaqRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function __construct(
        private readonly GetFaqsQueryHandler $getFaqsHandler,
        private readonly GetFaqByIdQueryHandler $getByIdHandler,
        private readonly CreateFaqHandler $createHandler,
        private readonly UpdateFaqHandler $updateHandler,
        private readonly DeleteFaqHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'orden'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json($this->getFaqsHandler->handle(new GetFaqsQuery($pagination)));
    }

    public function store(StoreFaqRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateFaqCommand(
            pregunta:    $request->pregunta,
            respuesta:   $request->respuesta,
            categoria:   $request->categoria,
            programa_id: $request->programa_id ? (int) $request->programa_id : null,
            orden:       $request->integer('orden', 0),
            activo:      $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetFaqByIdQuery($id)));
    }

    public function update(UpdateFaqRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateFaqCommand(
            id:          $id,
            pregunta:    $request->pregunta,
            respuesta:   $request->respuesta,
            categoria:   $request->categoria,
            programa_id: $request->has('programa_id') ? (int) $request->programa_id : null,
            orden:       $request->has('orden') ? $request->integer('orden') : null,
            activo:      $request->has('activo') ? $request->boolean('activo') : null,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteFaqCommand($id));

        return response()->json(null, 204);
    }
}
