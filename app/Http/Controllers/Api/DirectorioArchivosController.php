<?php

namespace App\Http\Controllers\Api;

use App\Application\DirectorioArchivos\Queries\GetArchivosParticipanteQuery;
use App\Application\DirectorioArchivos\Queries\GetCursosDirectorioQuery;
use App\Application\DirectorioArchivos\Queries\GetParticipantesCursoQuery;
use App\Application\DirectorioArchivos\QueryHandlers\GetArchivosParticipanteQueryHandler;
use App\Application\DirectorioArchivos\QueryHandlers\GetCursosDirectorioQueryHandler;
use App\Application\DirectorioArchivos\QueryHandlers\GetParticipantesCursoQueryHandler;
use App\Http\Controllers\Controller;
use App\Infrastructure\Vendedores\Services\VendedorScopeResolver;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DirectorioArchivosController extends Controller
{
    public function __construct(
        private readonly GetCursosDirectorioQueryHandler $cursosHandler,
        private readonly GetParticipantesCursoQueryHandler $participantesHandler,
        private readonly GetArchivosParticipanteQueryHandler $archivosHandler,
        private readonly VendedorScopeResolver $vendedorScope,
    ) {}

    public function cursos(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray($request->all());
        $idImpPermitidos = $this->vendedorScope->idImpPermitidos(auth()->user());

        return response()->json(
            $this->cursosHandler->handle(new GetCursosDirectorioQuery($pagination, $idImpPermitidos))
        );
    }

    public function participantes(Request $request, int $idImp): JsonResponse
    {
        $this->vendedorScope->assertAccesoImparte(auth()->user(), $idImp);

        $pagination = PaginationDTO::fromArray($request->all());

        return response()->json(
            $this->participantesHandler->handle(new GetParticipantesCursoQuery($idImp, $pagination))
        );
    }

    public function archivos(int $idIns): JsonResponse
    {
        $this->vendedorScope->assertAccesoInscripcion(auth()->user(), $idIns);

        return response()->json(
            $this->archivosHandler->handle(new GetArchivosParticipanteQuery($idIns))
        );
    }
}
