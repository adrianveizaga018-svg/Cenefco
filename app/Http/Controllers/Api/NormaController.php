<?php

namespace App\Http\Controllers\Api;

use App\Application\Normas\Commands\CreateNormaCommand;
use App\Application\Normas\Commands\DeleteNormaCommand;
use App\Application\Normas\Commands\UpdateNormaCommand;
use App\Application\Normas\Handlers\CreateNormaHandler;
use App\Application\Normas\Handlers\DeleteNormaHandler;
use App\Application\Normas\Handlers\UpdateNormaHandler;
use App\Application\Normas\Queries\GetPaginatedNormasQuery;
use App\Application\Normas\QueryHandlers\GetPaginatedNormasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Normas\StoreNormaRequest;
use App\Http\Requests\Normas\UpdateNormaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NormaController extends Controller
{
    public function __construct(
        private readonly GetPaginatedNormasQueryHandler $getPaginatedHandler,
        private readonly CreateNormaHandler $createHandler,
        private readonly UpdateNormaHandler $updateHandler,
        private readonly DeleteNormaHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = new PaginationDTO(
            pageIndex: (int) $request->get('page', 1),
            pageSize: (int) $request->get('per_page', 15),
            query: $request->get('q'),
            sortKey: $request->get('sort', 'fecha_promulgacion'),
            sortOrder: $request->get('order', 'desc'),
        );

        $filters = [
            'tipo_id' => $request->get('tipo_id'),
            'estado' => $request->get('estado'),
            'soloActivos' => $request->boolean('soloActivos', false),
        ];

        return response()->json($this->getPaginatedHandler->handle(new GetPaginatedNormasQuery($pagination, $filters)));
    }

    public function store(StoreNormaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateNormaCommand(
            tipo_norma_id: $request->tipo_norma_id,
            numero: $request->numero,
            titulo: $request->titulo,
            resumen: $request->resumen,
            texto_completo: $request->texto_completo,
            archivo_pdf_url: $request->archivo_pdf_url,
            fecha_promulgacion: $request->fecha_promulgacion,
            fecha_publicacion_gaceta: $request->fecha_publicacion_gaceta,
            estado_vigencia: $request->get('estado_vigencia', 'vigente'),
            tema_principal: $request->tema_principal,
            palabras_clave: $request->palabras_clave,
            publicado_por: $request->user()?->id,
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateNormaRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateNormaCommand($id, $request->validated())));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteNormaCommand($id));

        return response()->json(null, 204);
    }
}
