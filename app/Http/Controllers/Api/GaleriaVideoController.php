<?php

namespace App\Http\Controllers\Api;

use App\Application\GaleriasVideos\Commands\CreateGaleriaVideoCommand;
use App\Application\GaleriasVideos\Commands\DeleteGaleriaVideoCommand;
use App\Application\GaleriasVideos\Commands\UpdateGaleriaVideoCommand;
use App\Application\GaleriasVideos\Handlers\CreateGaleriaVideoHandler;
use App\Application\GaleriasVideos\Handlers\DeleteGaleriaVideoHandler;
use App\Application\GaleriasVideos\Handlers\UpdateGaleriaVideoHandler;
use App\Application\GaleriasVideos\Queries\GetGaleriaVideoByIdQuery;
use App\Application\GaleriasVideos\Queries\GetGaleriasVideosQuery;
use App\Application\GaleriasVideos\QueryHandlers\GetGaleriaVideoByIdQueryHandler;
use App\Application\GaleriasVideos\QueryHandlers\GetGaleriasVideosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\GaleriasVideos\StoreGaleriaVideoRequest;
use App\Http\Requests\GaleriasVideos\UpdateGaleriaVideoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GaleriaVideoController extends Controller
{
    public function __construct(
        private readonly GetGaleriasVideosQueryHandler $getVideosHandler,
        private readonly GetGaleriaVideoByIdQueryHandler $getByIdHandler,
        private readonly CreateGaleriaVideoHandler $createHandler,
        private readonly UpdateGaleriaVideoHandler $updateHandler,
        private readonly DeleteGaleriaVideoHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'orden'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getVideosHandler->handle(new GetGaleriasVideosQuery(
                $pagination,
                $request->boolean('soloActivos', false),
                $request->boolean('soloDestacados', false),
            ))
        );
    }

    public function store(StoreGaleriaVideoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateGaleriaVideoCommand(
            titulo:       $request->titulo,
            url_video:    $request->url_video,
            descripcion:  $request->descripcion,
            plataforma:   $request->input('plataforma', 'youtube'),
            video_id:     $request->video_id,
            miniatura_url: $request->miniatura_url,
            duracion:     $request->duracion,
            tipo:         $request->tipo,
            programa_id:  $request->programa_id ? (int) $request->programa_id : null,
            destacado:    $request->boolean('destacado', false),
            orden:        $request->integer('orden', 0),
            activo:       $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetGaleriaVideoByIdQuery($id)));
    }

    public function update(UpdateGaleriaVideoRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateGaleriaVideoCommand(
            id:           $id,
            titulo:       $request->titulo,
            url_video:    $request->url_video,
            descripcion:  $request->descripcion,
            plataforma:   $request->plataforma,
            video_id:     $request->video_id,
            miniatura_url: $request->miniatura_url,
            duracion:     $request->duracion,
            tipo:         $request->tipo,
            programa_id:  $request->has('programa_id') ? (int) $request->programa_id : null,
            destacado:    $request->has('destacado') ? $request->boolean('destacado') : null,
            orden:        $request->has('orden') ? $request->integer('orden') : null,
            activo:       $request->has('activo') ? $request->boolean('activo') : null,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteGaleriaVideoCommand($id));

        return response()->json(null, 204);
    }
}
