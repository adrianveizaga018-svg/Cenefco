<?php

namespace App\Http\Controllers\Api;

use App\Application\Honorarios\Commands\DeleteConfigHonorarioCommand;
use App\Application\Honorarios\Commands\UpsertConfigHonorarioCommand;
use App\Application\Honorarios\Handlers\DeleteConfigHonorarioHandler;
use App\Application\Honorarios\Handlers\UpsertConfigHonorarioHandler;
use App\Domain\Honorarios\Contracts\ConfigHonorarioRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Honorarios\UpsertConfigHonorarioRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Honorarios')]
class ConfigHonorarioController extends Controller
{
    public function __construct(
        private readonly ConfigHonorarioRepositoryInterface $repository,
        private readonly UpsertConfigHonorarioHandler        $upsertHandler,
        private readonly DeleteConfigHonorarioHandler        $deleteHandler,
    ) {}

    #[OA\Get(path: '/api/v1/config-honorarios', summary: 'Listar configuración de honorarios por programa', tags: ['Honorarios'])]
    public function index(): JsonResponse
    {
        return response()->json($this->repository->findAll());
    }

    #[OA\Post(path: '/api/v1/config-honorarios', summary: 'Configurar honorario de un programa', tags: ['Honorarios'])]
    public function upsert(UpsertConfigHonorarioRequest $request): JsonResponse
    {
        $dto = $this->upsertHandler->handle(new UpsertConfigHonorarioCommand(
            id_programa:    (int) $request->id_programa,
            tipo_honorario: $request->tipo_honorario,
            monto_fijo:     $request->filled('monto_fijo') ? (float) $request->monto_fijo : null,
            monto_por_dia:  $request->filled('monto_por_dia') ? (float) $request->monto_por_dia : null,
        ));

        return response()->json($dto, 201);
    }

    #[OA\Delete(path: '/api/v1/config-honorarios/{idPrograma}', summary: 'Eliminar configuración de honorario', tags: ['Honorarios'])]
    public function destroy(int $idPrograma): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteConfigHonorarioCommand($idPrograma));

        return response()->json(null, 204);
    }
}
