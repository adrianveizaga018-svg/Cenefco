<?php

namespace App\Http\Controllers\CitasAsesoria;

use App\Application\CitasAsesoria\Commands\CreateCitaAsesoriaCommand;
use App\Application\CitasAsesoria\Commands\DeleteCitaAsesoriaCommand;
use App\Application\CitasAsesoria\Commands\UpdateCitaAsesoriaCommand;
use App\Application\CitasAsesoria\Handlers\CreateCitaAsesoriaHandler;
use App\Application\CitasAsesoria\Handlers\DeleteCitaAsesoriaHandler;
use App\Application\CitasAsesoria\Handlers\UpdateCitaAsesoriaHandler;
use App\Application\CitasAsesoria\Queries\GetCitaAsesoriaByIdQuery;
use App\Application\CitasAsesoria\Queries\GetCitasAsesoriaQuery;
use App\Application\CitasAsesoria\QueryHandlers\GetCitaAsesoriaByIdQueryHandler;
use App\Application\CitasAsesoria\QueryHandlers\GetCitasAsesoriaQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CitasAsesoria\StoreCitaAsesoriaRequest;
use App\Http\Requests\CitasAsesoria\UpdateCitaAsesoriaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CitaAsesoriaController extends Controller
{
    public function __construct(
        private readonly GetCitasAsesoriaQueryHandler $listHandler,
        private readonly GetCitaAsesoriaByIdQueryHandler $showHandler,
        private readonly CreateCitaAsesoriaHandler $createHandler,
        private readonly UpdateCitaAsesoriaHandler $updateHandler,
        private readonly DeleteCitaAsesoriaHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
        ]);

        return response()->json($this->listHandler->handle(new GetCitasAsesoriaQuery(
            pagination: $pagination,
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetCitaAsesoriaByIdQuery($id)));
    }

    public function store(StoreCitaAsesoriaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCitaAsesoriaCommand(
            idCitaAsesoria: $request->integer('id_cita_asesoria'),
            idUs:           $request->integer('id_us'),
            idUsAsesor:     $request->integer('id_us_asesor') ?: null,
            fecha:          $request->string('fecha')->toString(),
            hora:           $request->input('hora'),
            motivo:         $request->input('motivo'),
            estadoCita:     $request->input('estado_cita'),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateCitaAsesoriaRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateCitaAsesoriaCommand(
            idCitaAsesoria: $id,
            idUs:           $request->integer('id_us') ?: null,
            idUsAsesor:     $request->integer('id_us_asesor') ?: null,
            fecha:          $request->input('fecha'),
            hora:           $request->input('hora'),
            motivo:         $request->input('motivo'),
            estadoCita:     $request->input('estado_cita'),
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteCitaAsesoriaCommand($id));

        return response()->json(null, 204);
    }
}
