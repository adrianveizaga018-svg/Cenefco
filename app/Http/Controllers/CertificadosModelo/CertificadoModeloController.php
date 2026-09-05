<?php

namespace App\Http\Controllers\CertificadosModelo;

use App\Application\CertificadosModelo\Commands\CreateCertificadoModeloCommand;
use App\Application\CertificadosModelo\Commands\DeleteCertificadoModeloCommand;
use App\Application\CertificadosModelo\Commands\UpdateCertificadoModeloCommand;
use App\Application\CertificadosModelo\Handlers\CreateCertificadoModeloHandler;
use App\Application\CertificadosModelo\Handlers\DeleteCertificadoModeloHandler;
use App\Application\CertificadosModelo\Handlers\UpdateCertificadoModeloHandler;
use App\Application\CertificadosModelo\Queries\GetCertificadoModeloByIdQuery;
use App\Application\CertificadosModelo\Queries\GetCertificadosModeloQuery;
use App\Application\CertificadosModelo\QueryHandlers\GetCertificadoModeloByIdQueryHandler;
use App\Application\CertificadosModelo\QueryHandlers\GetCertificadosModeloQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CertificadosModelo\StoreCertificadoModeloRequest;
use App\Http\Requests\CertificadosModelo\UpdateCertificadoModeloRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificadoModeloController extends Controller
{
    public function __construct(
        private readonly GetCertificadosModeloQueryHandler $listHandler,
        private readonly GetCertificadoModeloByIdQueryHandler $showHandler,
        private readonly CreateCertificadoModeloHandler $createHandler,
        private readonly UpdateCertificadoModeloHandler $updateHandler,
        private readonly DeleteCertificadoModeloHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
        ]);

        return response()->json($this->listHandler->handle(new GetCertificadosModeloQuery(
            pagination:   $pagination,
            conInactivos: (bool) $request->get('conInactivos', false),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetCertificadoModeloByIdQuery($id)));
    }

    public function store(StoreCertificadoModeloRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCertificadoModeloCommand(
            idCertmod:   $request->integer('id_certmod'),
            nombre:      $request->string('nombre')->toString(),
            descripcion: $request->input('descripcion'),
            urlImagen:   $request->input('url_imagen'),
            contenido:   $request->input('contenido'),
            idUsReg:     auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateCertificadoModeloRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateCertificadoModeloCommand(
            idCertmod:   $id,
            nombre:      $request->input('nombre'),
            descripcion: $request->input('descripcion'),
            urlImagen:   $request->input('url_imagen'),
            contenido:   $request->input('contenido'),
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteCertificadoModeloCommand($id));

        return response()->json(null, 204);
    }
}
