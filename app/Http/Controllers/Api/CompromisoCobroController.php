<?php

namespace App\Http\Controllers\Api;

use App\Application\CompromisosCobro\Commands\CancelarCompromisoCobroCommand;
use App\Application\CompromisosCobro\Commands\CreateCompromisoCobroCommand;
use App\Application\CompromisosCobro\Commands\MarcarCumplidoCompromisoCobroCommand;
use App\Application\CompromisosCobro\Commands\ReprogramarCompromisoCobroCommand;
use App\Application\CompromisosCobro\Handlers\CancelarCompromisoCobroHandler;
use App\Application\CompromisosCobro\Handlers\CreateCompromisoCobroHandler;
use App\Application\CompromisosCobro\Handlers\MarcarCumplidoCompromisoCobroHandler;
use App\Application\CompromisosCobro\Handlers\ReprogramarCompromisoCobroHandler;
use App\Application\CompromisosCobro\Queries\GetCompromisoCobroByIdQuery;
use App\Application\CompromisosCobro\Queries\GetCompromisoCobroLogQuery;
use App\Application\CompromisosCobro\Queries\GetCompromisosCobroQuery;
use App\Application\CompromisosCobro\Queries\GetResumenCompromisosCobroQuery;
use App\Application\CompromisosCobro\QueryHandlers\GetCompromisoCobroByIdQueryHandler;
use App\Application\CompromisosCobro\QueryHandlers\GetCompromisoCobroLogQueryHandler;
use App\Application\CompromisosCobro\QueryHandlers\GetCompromisosCobroQueryHandler;
use App\Application\CompromisosCobro\QueryHandlers\GetResumenCompromisosCobroQueryHandler;
use App\Domain\CompromisosCobro\Enums\MotivoReprogramacionEnum;
use App\Domain\Inscripciones\Contracts\InscripcionRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompromisosCobro\CancelarCompromisoCobroRequest;
use App\Http\Requests\CompromisosCobro\MarcarCumplidoCompromisoCobroRequest;
use App\Http\Requests\CompromisosCobro\ReprogramarCompromisoCobroRequest;
use App\Http\Requests\CompromisosCobro\StoreCompromisoCobroRequest;
use App\Infrastructure\Vendedores\Services\VendedorScopeResolver;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompromisoCobroController extends Controller
{
    public function __construct(
        private readonly GetCompromisosCobroQueryHandler        $getCompromisosHandler,
        private readonly GetResumenCompromisosCobroQueryHandler $getResumenHandler,
        private readonly GetCompromisoCobroLogQueryHandler      $getLogHandler,
        private readonly GetCompromisoCobroByIdQueryHandler     $getByIdHandler,
        private readonly CreateCompromisoCobroHandler           $createHandler,
        private readonly ReprogramarCompromisoCobroHandler      $reprogramarHandler,
        private readonly MarcarCumplidoCompromisoCobroHandler   $marcarCumplidoHandler,
        private readonly CancelarCompromisoCobroHandler         $cancelarHandler,
        private readonly VendedorScopeResolver                  $vendedorScope,
        private readonly InscripcionRepositoryInterface         $inscripcionRepository,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
            'query'     => $request->get('query', ''),
        ]);

        return response()->json(
            $this->getCompromisosHandler->handle(new GetCompromisosCobroQuery(
                pagination: $pagination,
                estado:     $request->filled('estado') ? $request->get('estado') : null,
                idImpPermitidos: $this->vendedorScope->idImpPermitidos(auth()->user()),
            ))
        );
    }

    public function resumen(): JsonResponse
    {
        return response()->json(
            $this->getResumenHandler->handle(new GetResumenCompromisosCobroQuery(
                idImpPermitidos: $this->vendedorScope->idImpPermitidos(auth()->user()),
            ))
        );
    }

    public function store(StoreCompromisoCobroRequest $request): JsonResponse
    {
        $idIns = (int) $request->id_ins;
        $insDto = $this->inscripcionRepository->findById($idIns);
        $this->vendedorScope->assertAccesoImparte(auth()->user(), $insDto->id_imp);

        $dto = $this->createHandler->handle(new CreateCompromisoCobroCommand(
            idIns:              $idIns,
            idUs:                $insDto->id_us,
            idImp:               $insDto->id_imp,
            fechaCompromiso:     $request->fecha_compromiso,
            horaCompromiso:      $request->filled('hora_compromiso') ? $request->hora_compromiso : null,
            montoComprometido:   $request->filled('monto_comprometido') ? (float) $request->monto_comprometido : null,
            observacion:         $request->observacion,
            registradoPor:       auth()->id() ?? 0,
        ));

        return response()->json($dto, 201);
    }

    public function reprogramar(ReprogramarCompromisoCobroRequest $request, int $id): JsonResponse
    {
        $dto = $this->reprogramarHandler->handle(new ReprogramarCompromisoCobroCommand(
            id:            $id,
            nuevaFecha:    $request->nueva_fecha,
            nuevaHora:     $request->filled('nueva_hora') ? $request->nueva_hora : null,
            motivo:        MotivoReprogramacionEnum::from($request->motivo),
            observacion:   $request->observacion,
            registradoPor: auth()->id() ?? 0,
        ));

        return response()->json($dto);
    }

    public function cumplir(MarcarCumplidoCompromisoCobroRequest $request, int $id): JsonResponse
    {
        $dto = $this->marcarCumplidoHandler->handle(new MarcarCumplidoCompromisoCobroCommand(
            id:            $id,
            registradoPor: auth()->id() ?? 0,
            observacion:   $request->observacion,
        ));

        return response()->json($dto);
    }

    public function historial(int $id): JsonResponse
    {
        $compromiso = $this->getByIdHandler->handle(new GetCompromisoCobroByIdQuery($id));
        $this->vendedorScope->assertAccesoImparte(auth()->user(), $compromiso->id_imp);

        return response()->json(
            $this->getLogHandler->handle(new GetCompromisoCobroLogQuery($id))
        );
    }

    public function cancelar(CancelarCompromisoCobroRequest $request, int $id): JsonResponse
    {
        $dto = $this->cancelarHandler->handle(new CancelarCompromisoCobroCommand(
            id:            $id,
            registradoPor: auth()->id() ?? 0,
            observacion:   $request->observacion,
        ));

        return response()->json($dto);
    }
}
