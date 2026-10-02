<?php

namespace App\Http\Controllers\Api;

use App\Application\CorreosEnviados\Queries\GetCorreosEnviadosQuery;
use App\Application\CorreosEnviados\QueryHandlers\GetCorreosEnviadosQueryHandler;
use App\Http\Controllers\Controller;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CorreoEnviadoController extends Controller
{
    public function __construct(
        private readonly GetCorreosEnviadosQueryHandler $getCorreosEnviadosHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
            'query'     => $request->get('query', ''),
        ]);

        return response()->json(
            $this->getCorreosEnviadosHandler->handle(new GetCorreosEnviadosQuery(
                pagination:      $pagination,
                referenciaTipo:  $request->filled('referencia_tipo') ? $request->get('referencia_tipo') : null,
                referenciaId:    $request->filled('referencia_id') ? $request->integer('referencia_id') : null,
            ))
        );
    }
}
