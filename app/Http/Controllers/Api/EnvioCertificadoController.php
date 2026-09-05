<?php

namespace App\Http\Controllers\Api;

use App\Application\EnviosCertificado\Commands\CreateEnvioCertificadoCommand;
use App\Application\EnviosCertificado\Commands\DeleteEnvioCertificadoCommand;
use App\Application\EnviosCertificado\Handlers\CreateEnvioCertificadoHandler;
use App\Application\EnviosCertificado\Handlers\DeleteEnvioCertificadoHandler;
use App\Application\EnviosCertificado\Queries\GetEnviosCertificadoQuery;
use App\Application\EnviosCertificado\QueryHandlers\GetEnviosCertificadoQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\EnviosCertificado\StoreEnvioCertificadoRequest;
use App\Infrastructure\Vendedores\Services\VendedorScopeResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnvioCertificadoController extends Controller
{
    public function __construct(
        private readonly GetEnviosCertificadoQueryHandler $getListHandler,
        private readonly CreateEnvioCertificadoHandler    $createHandler,
        private readonly DeleteEnvioCertificadoHandler    $deleteHandler,
        private readonly VendedorScopeResolver $vendedorScope,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $request->validate(['id_ins' => ['required', 'integer']]);
        $idIns = (int) $request->id_ins;

        $this->vendedorScope->assertAccesoInscripcion(auth()->user(), $idIns);

        return response()->json(
            $this->getListHandler->handle(new GetEnviosCertificadoQuery($idIns))
        );
    }

    public function store(StoreEnvioCertificadoRequest $request): JsonResponse
    {
        if (! auth()->user()->esAdmin()) {
            return response()->json(['error' => 'No autorizado.'], 403);
        }

        $imagenPath = $request->file('imagen_guia')->store('envios-certificado', 'public');

        $dto = $this->createHandler->handle(new CreateEnvioCertificadoCommand(
            id_ins:         (int) $request->id_ins,
            ciudad_destino: $request->ciudad_destino,
            fecha_envio:    $request->fecha_envio,
            imagen_guia:    $imagenPath,
            aclaraciones:   $request->aclaraciones,
            costo:          $request->filled('costo') ? (float) $request->costo : null,
            id_us_reg:      auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function destroy(int $id): JsonResponse
    {
        if (! auth()->user()->esAdmin()) {
            return response()->json(['error' => 'No autorizado.'], 403);
        }

        $this->deleteHandler->handle(new DeleteEnvioCertificadoCommand($id));

        return response()->json(null, 204);
    }
}
