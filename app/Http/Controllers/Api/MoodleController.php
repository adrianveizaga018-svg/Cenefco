<?php

namespace App\Http\Controllers\Api;

use App\Application\Moodle\Commands\CreateMoodleCommand;
use App\Application\Moodle\Commands\DeleteMoodleCommand;
use App\Application\Moodle\Commands\UpdateMoodleCommand;
use App\Application\Moodle\Handlers\CreateMoodleHandler;
use App\Application\Moodle\Handlers\DeleteMoodleHandler;
use App\Application\Moodle\Handlers\UpdateMoodleHandler;
use App\Application\Moodle\Queries\GetMoodleByIdQuery;
use App\Application\Moodle\Queries\GetMoodlesQuery;
use App\Application\Moodle\QueryHandlers\GetMoodleByIdQueryHandler;
use App\Application\Moodle\QueryHandlers\GetMoodlesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Moodle\StoreMoodleRequest;
use App\Http\Requests\Moodle\UpdateMoodleRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MoodleController extends Controller
{
    public function __construct(
        private readonly GetMoodlesQueryHandler $listHandler,
        private readonly GetMoodleByIdQueryHandler $showHandler,
        private readonly CreateMoodleHandler $createHandler,
        private readonly UpdateMoodleHandler $updateHandler,
        private readonly DeleteMoodleHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
        ]);

        return response()->json($this->listHandler->handle(new GetMoodlesQuery(
            pagination:   $pagination,
            query:        $request->input('query'),
            conInactivos: (bool) $request->get('conInactivos', false),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetMoodleByIdQuery($id)));
    }

    public function store(StoreMoodleRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateMoodleCommand(
            idMoodle:           $request->integer('id_moodle'),
            numMoodle:          $request->integer('num_moodle') ?: null,
            tituloMoodle:       $request->string('titulo_moodle')->toString(),
            cpMoodleServidor:   $request->input('cp_moodle_servidor'),
            cpMoodleBaseDatos:  $request->input('cp_moodle_base_datos'),
            cpMoodleUsuarioBd:  $request->input('cp_moodle_usuario_bd'),
            cpMoodleContrasena: $request->input('cp_moodle_contrasena'),
            cpUrlCampus:        $request->input('cp_url_campus'),
            idUsReg:            auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateMoodleRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateMoodleCommand(
            idMoodle:           $id,
            tituloMoodle:       $request->input('titulo_moodle'),
            cpMoodleServidor:   $request->input('cp_moodle_servidor'),
            cpMoodleBaseDatos:  $request->input('cp_moodle_base_datos'),
            cpMoodleUsuarioBd:  $request->input('cp_moodle_usuario_bd'),
            cpMoodleContrasena: $request->input('cp_moodle_contrasena'),
            cpUrlCampus:        $request->input('cp_url_campus'),
            estado:             $request->integer('estado') ?: null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteMoodleCommand($id));

        return response()->json(null, 204);
    }
}
