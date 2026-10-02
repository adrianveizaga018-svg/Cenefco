<?php

namespace App\Http\Controllers\Api;

use App\Application\Trivia\Commands\CancelarTriviaCanjeCommand;
use App\Application\Trivia\Commands\CanjearTriviaPremioCommand;
use App\Application\Trivia\Commands\MarcarTriviaCanjeEntregadoCommand;
use App\Application\Trivia\Handlers\CancelarTriviaCanjeHandler;
use App\Application\Trivia\Handlers\CanjearTriviaPremioHandler;
use App\Application\Trivia\Handlers\MarcarTriviaCanjeEntregadoHandler;
use App\Application\Trivia\Queries\GetMisTriviaCanjesQuery;
use App\Application\Trivia\Queries\GetTriviaCanjesQuery;
use App\Application\Trivia\Queries\GetTriviaSaldoQuery;
use App\Application\Trivia\QueryHandlers\GetMisTriviaCanjesQueryHandler;
use App\Application\Trivia\QueryHandlers\GetTriviaCanjesQueryHandler;
use App\Application\Trivia\QueryHandlers\GetTriviaSaldoQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Trivia\ResolverTriviaCanjeRequest;
use App\Http\Requests\Trivia\StoreTriviaCanjeRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TriviaCanjeController extends Controller
{
    public function __construct(
        private readonly GetTriviaCanjesQueryHandler $getCanjesHandler,
        private readonly GetMisTriviaCanjesQueryHandler $getMisCanjesHandler,
        private readonly GetTriviaSaldoQueryHandler $getSaldoHandler,
        private readonly CanjearTriviaPremioHandler $canjearHandler,
        private readonly MarcarTriviaCanjeEntregadoHandler $entregarHandler,
        private readonly CancelarTriviaCanjeHandler $cancelarHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize' => $request->get('pageSize', 15),
            'query' => $request->get('query', ''),
        ]);

        return response()->json(
            $this->getCanjesHandler->handle(new GetTriviaCanjesQuery($pagination, [
                'estado' => $request->get('estado'),
            ]))
        );
    }

    public function entregar(ResolverTriviaCanjeRequest $request, int $id): JsonResponse
    {
        $dto = $this->entregarHandler->handle(new MarcarTriviaCanjeEntregadoCommand(
            id: $id,
            entregadoPor: auth()->id(),
            nota: $request->nota,
        ));

        return response()->json($dto);
    }

    public function cancelar(ResolverTriviaCanjeRequest $request, int $id): JsonResponse
    {
        $dto = $this->cancelarHandler->handle(new CancelarTriviaCanjeCommand(
            id: $id,
            canceladoPor: auth()->id(),
            nota: $request->nota,
        ));

        return response()->json($dto);
    }

    public function saldo(Request $request): JsonResponse
    {
        return response()->json(
            $this->getSaldoHandler->handle(new GetTriviaSaldoQuery($request->user()->id))
        );
    }

    public function canjear(StoreTriviaCanjeRequest $request): JsonResponse
    {
        $dto = $this->canjearHandler->handle(new CanjearTriviaPremioCommand(
            usuarioId: $request->user()->id,
            premioId: (int) $request->premio_id,
        ));

        return response()->json($dto, 201);
    }

    public function misCanjes(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->getMisCanjesHandler->handle(new GetMisTriviaCanjesQuery($request->user()->id)),
        ]);
    }
}
