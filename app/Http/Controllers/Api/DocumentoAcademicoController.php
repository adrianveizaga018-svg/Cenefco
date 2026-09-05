<?php

namespace App\Http\Controllers\Api;

use App\Application\DocumentosAcademicos\Commands\CreateDocumentoAcademicoCommand;
use App\Application\DocumentosAcademicos\Commands\DeleteDocumentoAcademicoCommand;
use App\Application\DocumentosAcademicos\Commands\UpdateDocumentoAcademicoCommand;
use App\Application\DocumentosAcademicos\Handlers\CreateDocumentoAcademicoHandler;
use App\Application\DocumentosAcademicos\Handlers\DeleteDocumentoAcademicoHandler;
use App\Application\DocumentosAcademicos\Handlers\UpdateDocumentoAcademicoHandler;
use App\Application\DocumentosAcademicos\Queries\GetDocumentoAcademicoByIdQuery;
use App\Application\DocumentosAcademicos\Queries\GetDocumentosAcademicosQuery;
use App\Application\DocumentosAcademicos\QueryHandlers\GetDocumentoAcademicoByIdQueryHandler;
use App\Application\DocumentosAcademicos\QueryHandlers\GetDocumentosAcademicosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentosAcademicos\StoreDocumentoAcademicoRequest;
use App\Http\Requests\DocumentosAcademicos\UpdateDocumentoAcademicoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentoAcademicoController extends Controller
{
    public function __construct(
        private readonly GetDocumentosAcademicosQueryHandler  $getDocumentosHandler,
        private readonly GetDocumentoAcademicoByIdQueryHandler $getDocumentoByIdHandler,
        private readonly CreateDocumentoAcademicoHandler       $createHandler,
        private readonly UpdateDocumentoAcademicoHandler       $updateHandler,
        private readonly DeleteDocumentoAcademicoHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'id_documento'),
            'sortOrder' => $request->input('sort.order', 'desc'),
        ]);

        return response()->json(
            $this->getDocumentosHandler->handle(new GetDocumentosAcademicosQuery(
                pagination:   $pagination,
                conInactivos: $request->boolean('conInactivos', false),
                idUs:         $request->filled('id_us') ? (int) $request->get('id_us') : null,
                idFechapago:  $request->filled('id_fechapago') ? (int) $request->get('id_fechapago') : null,
                idFechadoc:   $request->filled('id_fechadoc') ? (int) $request->get('id_fechadoc') : null,
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getDocumentoByIdHandler->handle(new GetDocumentoAcademicoByIdQuery($id))
        );
    }

    public function store(StoreDocumentoAcademicoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateDocumentoAcademicoCommand(
            id_documento:          $request->integer('id_documento'),
            id_us_reg:             $request->filled('id_us_reg') ? $request->integer('id_us_reg') : 0,
            num_documento:         $request->filled('num_documento') ? $request->integer('num_documento') : 0,
            id_us:                 $request->integer('id_us'),
            id_fechapago:          $request->filled('id_fechapago') ? $request->integer('id_fechapago') : null,
            id_fechadoc:           $request->filled('id_fechadoc') ? $request->integer('id_fechadoc') : null,
            fecha_dejo_fisico:     $request->input('fecha_dejo_fisico'),
            dejo_documento_fisico: $request->filled('dejo_documento_fisico') ? $request->integer('dejo_documento_fisico') : null,
            documento_digital:     $request->input('documento_digital'),
            observacion_doc:       $request->input('observacion_doc'),
            estado:                $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateDocumentoAcademicoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateDocumentoAcademicoCommand(
            id:                    $id,
            id_fechapago:          $request->filled('id_fechapago') ? $request->integer('id_fechapago') : null,
            id_fechadoc:           $request->filled('id_fechadoc') ? $request->integer('id_fechadoc') : null,
            fecha_dejo_fisico:     $request->input('fecha_dejo_fisico'),
            dejo_documento_fisico: $request->filled('dejo_documento_fisico') ? $request->integer('dejo_documento_fisico') : null,
            documento_digital:     $request->input('documento_digital'),
            observacion_doc:       $request->input('observacion_doc'),
            estado:                $request->filled('estado') ? $request->integer('estado') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteDocumentoAcademicoCommand($id));

        return response()->json(null, 204);
    }
}
