<?php

namespace App\Http\Controllers\Api;

use App\Application\UsuariosMoodle\Commands\CreateUsuarioMoodleCommand;
use App\Application\UsuariosMoodle\Commands\DeleteUsuarioMoodleCommand;
use App\Application\UsuariosMoodle\Commands\UpdateUsuarioMoodleCommand;
use App\Application\UsuariosMoodle\Handlers\CreateUsuarioMoodleHandler;
use App\Application\UsuariosMoodle\Handlers\DeleteUsuarioMoodleHandler;
use App\Application\UsuariosMoodle\Handlers\UpdateUsuarioMoodleHandler;
use App\Application\UsuariosMoodle\Queries\GetUsuarioMoodleByIdQuery;
use App\Application\UsuariosMoodle\Queries\GetUsuariosMoodleQuery;
use App\Application\UsuariosMoodle\QueryHandlers\GetUsuarioMoodleByIdQueryHandler;
use App\Application\UsuariosMoodle\QueryHandlers\GetUsuariosMoodleQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\UsuariosMoodle\StoreUsuarioMoodleRequest;
use App\Http\Requests\UsuariosMoodle\UpdateUsuarioMoodleRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsuarioMoodleController extends Controller
{
    public function __construct(
        private readonly GetUsuariosMoodleQueryHandler    $listHandler,
        private readonly GetUsuarioMoodleByIdQueryHandler $showHandler,
        private readonly CreateUsuarioMoodleHandler       $createHandler,
        private readonly UpdateUsuarioMoodleHandler       $updateHandler,
        private readonly DeleteUsuarioMoodleHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetUsuariosMoodleQuery(
                pagination:   $pagination,
                idUs:         $request->filled('id_us') ? (int) $request->get('id_us') : null,
                idMoodle:     $request->filled('id_moodle') ? (int) $request->get('id_moodle') : null,
                conInactivos: $request->boolean('conInactivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetUsuarioMoodleByIdQuery($id))
        );
    }

    public function store(StoreUsuarioMoodleRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateUsuarioMoodleCommand(
            id_usmoodle:    $request->integer('id_usmoodle'),
            id_us:          $request->integer('id_us'),
            id_us_reg:      $request->integer('id_us_reg', 0),
            num_usmoodle:   $request->integer('num_usmoodle', 0),
            id_moodle:      $request->integer('id_moodle'),
            moodle_id_user: $request->input('moodle_id_user'),
            estado:         $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateUsuarioMoodleRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateUsuarioMoodleCommand(
            id:             $id,
            id_moodle:      $request->filled('id_moodle') ? $request->integer('id_moodle') : null,
            moodle_id_user: $request->input('moodle_id_user'),
            estado:         $request->filled('estado') ? $request->integer('estado') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteUsuarioMoodleCommand($id));

        return response()->json(null, 204);
    }
}
