<?php

namespace App\Http\Controllers\Api;

use App\Application\Testimonios\Commands\CreateTestimonioCommand;
use App\Application\Testimonios\Commands\DeleteTestimonioCommand;
use App\Application\Testimonios\Commands\UpdateTestimonioCommand;
use App\Application\Testimonios\Handlers\CreateTestimonioHandler;
use App\Application\Testimonios\Handlers\DeleteTestimonioHandler;
use App\Application\Testimonios\Handlers\UpdateTestimonioHandler;
use App\Application\Testimonios\Queries\GetTestimonioByIdQuery;
use App\Application\Testimonios\Queries\GetTestimonioBySlugQuery;
use App\Application\Testimonios\Queries\GetTestimoniosQuery;
use App\Application\Testimonios\QueryHandlers\GetTestimonioByIdQueryHandler;
use App\Application\Testimonios\QueryHandlers\GetTestimonioBySlugQueryHandler;
use App\Application\Testimonios\QueryHandlers\GetTestimoniosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Testimonios\StoreTestimonioRequest;
use App\Http\Requests\Testimonios\UpdateTestimonioRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestimonioController extends Controller
{
    public function __construct(
        private readonly GetTestimoniosQueryHandler      $getTestimoniosHandler,
        private readonly GetTestimonioByIdQueryHandler   $getByIdHandler,
        private readonly GetTestimonioBySlugQueryHandler $getBySlugHandler,
        private readonly CreateTestimonioHandler         $createHandler,
        private readonly UpdateTestimonioHandler         $updateHandler,
        private readonly DeleteTestimonioHandler         $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'orden'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getTestimoniosHandler->handle(
                new GetTestimoniosQuery($pagination, $request->boolean('soloDestacados', false))
            )
        );
    }

    public function store(StoreTestimonioRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateTestimonioCommand(
            nombre:       $request->nombre,
            testimonio:   $request->testimonio,
            cargo:        $request->cargo,
            empresa:      $request->empresa,
            calificacion: $request->integer('calificacion', 5),
            foto_url:     $request->foto_url,
            foto_alt:     $request->foto_alt,
            programa_id:  $request->programa_id ? (int) $request->programa_id : null,
            destacado:    $request->boolean('destacado', false),
            orden:        $request->integer('orden', 0),
            estado:       $request->input('estado', 'publicado'),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetTestimonioByIdQuery($id)));
    }

    public function showBySlug(string $slug): JsonResponse
    {
        return response()->json($this->getBySlugHandler->handle(new GetTestimonioBySlugQuery($slug)));
    }

    public function update(UpdateTestimonioRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateTestimonioCommand(
            id:           $id,
            nombre:       $request->nombre,
            testimonio:   $request->testimonio,
            cargo:        $request->cargo,
            empresa:      $request->empresa,
            calificacion: $request->has('calificacion') ? $request->integer('calificacion') : null,
            foto_url:     $request->foto_url,
            foto_alt:     $request->foto_alt,
            programa_id:  $request->has('programa_id') ? (int) $request->programa_id : null,
            destacado:    $request->has('destacado') ? $request->boolean('destacado') : null,
            orden:        $request->has('orden') ? $request->integer('orden') : null,
            estado:       $request->estado,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteTestimonioCommand($id));

        return response()->json(null, 204);
    }
}
