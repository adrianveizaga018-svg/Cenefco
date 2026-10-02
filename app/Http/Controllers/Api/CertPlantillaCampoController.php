<?php

namespace App\Http\Controllers\Api;

use App\Application\CertPlantillaCampos\Commands\CreateCertPlantillaCampoCommand;
use App\Application\CertPlantillaCampos\Commands\DeleteCertPlantillaCampoCommand;
use App\Application\CertPlantillaCampos\Commands\UpdateCertPlantillaCampoCommand;
use App\Application\CertPlantillaCampos\Handlers\CreateCertPlantillaCampoHandler;
use App\Application\CertPlantillaCampos\Handlers\DeleteCertPlantillaCampoHandler;
use App\Application\CertPlantillaCampos\Handlers\UpdateCertPlantillaCampoHandler;
use App\Application\CertPlantillaCampos\Queries\GetCertPlantillaCampoByIdQuery;
use App\Application\CertPlantillaCampos\Queries\GetCertPlantillaCamposQuery;
use App\Application\CertPlantillaCampos\QueryHandlers\GetCertPlantillaCampoByIdQueryHandler;
use App\Application\CertPlantillaCampos\QueryHandlers\GetCertPlantillaCamposQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CertPlantillaCampos\StoreCertPlantillaCampoRequest;
use App\Http\Requests\CertPlantillaCampos\UpdateCertPlantillaCampoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertPlantillaCampoController extends Controller
{
    public function __construct(
        private readonly GetCertPlantillaCamposQueryHandler    $listHandler,
        private readonly GetCertPlantillaCampoByIdQueryHandler $showHandler,
        private readonly CreateCertPlantillaCampoHandler       $createHandler,
        private readonly UpdateCertPlantillaCampoHandler       $updateHandler,
        private readonly DeleteCertPlantillaCampoHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 100),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetCertPlantillaCamposQuery(
                pagination:  $pagination,
                plantillaId: $request->filled('plantilla_id') ? (int) $request->get('plantilla_id') : null,
                soloActivos: $request->boolean('soloActivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetCertPlantillaCampoByIdQuery($id))
        );
    }

    public function store(StoreCertPlantillaCampoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCertPlantillaCampoCommand(
            plantilla_id: $request->integer('plantilla_id'),
            clave:        $request->input('clave'),
            etiqueta:     $request->input('etiqueta', $request->input('clave')),
            tipo:         $request->input('tipo', 'texto'),
            pos_x_pct:    (float) $request->input('pos_x_pct', 50),
            pos_y_pct:    (float) $request->input('pos_y_pct', 50),
            ancho_pct:    $request->filled('ancho_pct') ? (float) $request->input('ancho_pct') : null,
            alto_pct:     $request->filled('alto_pct')  ? (float) $request->input('alto_pct')  : null,
            fuente:       $request->input('fuente'),
            tamano_pt:    $request->integer('tamano_pt', 36),
            color:        $request->input('color', '#000000'),
            alineacion:   $request->input('alineacion', 'center'),
            negrita:      $request->boolean('negrita', false),
            cursiva:      $request->boolean('cursiva', false),
            mayusculas:   $request->input('mayusculas', 'none'),
            valor_fijo:   $request->input('valor_fijo'),
            activo:       $request->boolean('activo', true),
            orden:        $request->integer('orden', 0),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateCertPlantillaCampoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateCertPlantillaCampoCommand(
            id:         $id,
            clave:      $request->filled('clave')      ? $request->input('clave')                : null,
            etiqueta:   $request->has('etiqueta')      ? $request->input('etiqueta')             : null,
            tipo:       $request->filled('tipo')       ? $request->input('tipo')                 : null,
            pos_x_pct:  $request->filled('pos_x_pct') ? (float) $request->input('pos_x_pct')   : null,
            pos_y_pct:  $request->filled('pos_y_pct') ? (float) $request->input('pos_y_pct')   : null,
            ancho_pct:  $request->filled('ancho_pct') ? (float) $request->input('ancho_pct')   : null,
            alto_pct:   $request->filled('alto_pct')  ? (float) $request->input('alto_pct')    : null,
            fuente:     $request->filled('fuente')    ? $request->input('fuente')               : null,
            tamano_pt:  $request->filled('tamano_pt') ? $request->integer('tamano_pt')          : null,
            color:      $request->filled('color')     ? $request->input('color')                : null,
            alineacion: $request->filled('alineacion')? $request->input('alineacion')           : null,
            negrita:    $request->has('negrita')      ? ($request->boolean('negrita') ? 1 : 0)  : null,
            cursiva:    $request->has('cursiva')      ? ($request->boolean('cursiva') ? 1 : 0)  : null,
            mayusculas: $request->filled('mayusculas')? $request->input('mayusculas')           : null,
            valor_fijo: $request->has('valor_fijo')   ? $request->input('valor_fijo')           : null,
            activo:     $request->has('activo')       ? ($request->boolean('activo') ? 1 : 0)   : null,
            orden:      $request->filled('orden')     ? $request->integer('orden')              : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteCertPlantillaCampoCommand($id));

        return response()->json(null, 204);
    }
}
