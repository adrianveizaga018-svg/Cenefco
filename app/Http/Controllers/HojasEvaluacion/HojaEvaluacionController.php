<?php

namespace App\Http\Controllers\HojasEvaluacion;

use App\Application\HojasEvaluacion\Commands\CreateHojaEvaluacionCommand;
use App\Application\HojasEvaluacion\Commands\DeleteHojaEvaluacionCommand;
use App\Application\HojasEvaluacion\Commands\UpdateHojaEvaluacionCommand;
use App\Application\HojasEvaluacion\Handlers\CreateHojaEvaluacionHandler;
use App\Application\HojasEvaluacion\Handlers\DeleteHojaEvaluacionHandler;
use App\Application\HojasEvaluacion\Handlers\UpdateHojaEvaluacionHandler;
use App\Application\HojasEvaluacion\Queries\GetHojaEvaluacionByIdQuery;
use App\Application\HojasEvaluacion\Queries\GetHojasEvaluacionQuery;
use App\Application\HojasEvaluacion\QueryHandlers\GetHojaEvaluacionByIdQueryHandler;
use App\Application\HojasEvaluacion\QueryHandlers\GetHojasEvaluacionQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\HojasEvaluacion\StoreHojaEvaluacionRequest;
use App\Http\Requests\HojasEvaluacion\UpdateHojaEvaluacionRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HojaEvaluacionController extends Controller
{
    public function __construct(
        private readonly GetHojasEvaluacionQueryHandler $listHandler,
        private readonly GetHojaEvaluacionByIdQueryHandler $showHandler,
        private readonly CreateHojaEvaluacionHandler $createHandler,
        private readonly UpdateHojaEvaluacionHandler $updateHandler,
        private readonly DeleteHojaEvaluacionHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
        ]);

        return response()->json($this->listHandler->handle(new GetHojasEvaluacionQuery(
            pagination:   $pagination,
            idUs:         $request->integer('id_us') ?: null,
            idUsTut:      $request->integer('id_us_tut') ?: null,
            conInactivos: (bool) $request->get('conInactivos', false),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetHojaEvaluacionByIdQuery($id)));
    }

    public function store(StoreHojaEvaluacionRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateHojaEvaluacionCommand(
            idHojaevaluacion: $request->integer('id_hojaevaluacion'),
            idUs:             $request->integer('id_us'),
            idUsTut:          $request->integer('id_us_tut') ?: null,
            descripcion:      $request->input('descripcion'),
            fecha:            $request->input('fecha'),
            puntaje:          $request->input('puntaje') !== null ? (float) $request->input('puntaje') : null,
            idUsReg:          auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateHojaEvaluacionRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateHojaEvaluacionCommand(
            idHojaevaluacion: $id,
            idUs:             $request->integer('id_us') ?: null,
            idUsTut:          $request->integer('id_us_tut') ?: null,
            descripcion:      $request->input('descripcion'),
            fecha:            $request->input('fecha'),
            puntaje:          $request->input('puntaje') !== null ? (float) $request->input('puntaje') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteHojaEvaluacionCommand($id));

        return response()->json(null, 204);
    }
}
