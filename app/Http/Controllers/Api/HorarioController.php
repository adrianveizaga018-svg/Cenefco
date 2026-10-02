<?php

namespace App\Http\Controllers\Api;

use App\Application\Horarios\Commands\CreateHorarioCommand;
use App\Application\Horarios\Commands\DeleteHorarioCommand;
use App\Application\Horarios\Commands\UpdateHorarioCommand;
use App\Application\Horarios\Handlers\CreateHorarioHandler;
use App\Application\Horarios\Handlers\DeleteHorarioHandler;
use App\Application\Horarios\Handlers\UpdateHorarioHandler;
use App\Application\Horarios\Queries\GetHorarioByIdQuery;
use App\Application\Horarios\Queries\GetHorariosQuery;
use App\Application\Horarios\QueryHandlers\GetHorarioByIdQueryHandler;
use App\Application\Horarios\QueryHandlers\GetHorariosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Horarios\StoreHorarioRequest;
use App\Http\Requests\Horarios\UpdateHorarioRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function __construct(
        private readonly GetHorariosQueryHandler $getHorariosHandler,
        private readonly GetHorarioByIdQueryHandler $getHorarioByIdHandler,
        private readonly CreateHorarioHandler $createHandler,
        private readonly UpdateHorarioHandler $updateHandler,
        private readonly DeleteHorarioHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
            'query'     => '',
            'sortKey'   => 'hora_inicio',
            'sortOrder' => 'asc',
        ]);

        return response()->json(
            $this->getHorariosHandler->handle(new GetHorariosQuery(
                pagination:   $pagination,
                conInactivos: $request->boolean('conInactivos', false),
                idImp:        $request->has('id_imp') ? $request->integer('id_imp') : null,
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getHorarioByIdHandler->handle(new GetHorarioByIdQuery($id))
        );
    }

    public function store(StoreHorarioRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateHorarioCommand(
            id_horar:    $request->integer('id_horar'),
            id_us_reg:   $request->integer('id_us_reg', 0),
            id_imp:      $request->integer('id_imp'),
            id_d:        $request->filled('id_d') ? $request->integer('id_d') : null,
            hora_inicio: $request->hora_inicio,
            hora_fin:    $request->hora_fin,
            periodos:    $request->periodos,
            estado:      $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateHorarioRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateHorarioCommand(
            id:          $id,
            id_d:        $request->filled('id_d') ? $request->integer('id_d') : null,
            hora_inicio: $request->hora_inicio,
            hora_fin:    $request->hora_fin,
            periodos:    $request->periodos,
            estado:      $request->filled('estado') ? $request->integer('estado') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteHorarioCommand($id));

        return response()->json(null, 204);
    }
}
